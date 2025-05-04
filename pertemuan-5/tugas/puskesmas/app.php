<?php
require "config.php";

function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $table = $_POST['table'] ?? '';
    $id = $_POST['id'] ?? null;

    $fields = $values = [];
    foreach ($_POST as $key => $value) {
        if (!in_array($key, ['action', 'table', 'id'])) {
            $fields[] = $key;
            $values[":$key"] = $value;
        }
    }

    try {
        if ($action === 'add') {
            $sql = "INSERT INTO $table (" . implode(', ', $fields) . ") VALUES (" . implode(', ', array_keys($values)) . ")";
        } elseif ($action === 'update') {
            $setClause = implode(', ', array_map(fn($field) => "$field = :$field", $fields));
            $sql = "UPDATE $table SET $setClause WHERE id = :id";
            $values[':id'] = $id;
        } elseif ($action === 'delete' && $id) {
            $sql = "DELETE FROM $table WHERE id = :id";
            $values = [':id' => $id];
        }
        $stmt = $dbh->prepare($sql);
        $stmt->execute($values);
        header("Location: app.php?table=$table&msg=" . ($action === 'add' ? 'added' : ($action === 'update' ? 'updated' : 'deleted')));
        exit;
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Fetch data
$currentTable = $_GET['table'] ?? 'pasien';
$editId = $_GET['edit'] ?? null;
$editData = $editId ? fetchById($dbh, $currentTable, $editId) : null;
$tableData = fetchAll($dbh, $currentTable);
$tableStructure = $dbh->query("DESCRIBE $currentTable")->fetchAll();
$relationships = [
    'pasien' => ['kelurahan_id' => ['table' => 'kelurahan', 'display' => 'nama']],
    'paramedis' => ['unit_kerja_id' => ['table' => 'unit_kerja', 'display' => 'nama']],
    'periksa' => [
        'pasien_id' => ['table' => 'pasien', 'display' => 'nama'],
        'dokter_id' => ['table' => 'paramedis', 'display' => 'nama', 'condition' => "kategori='dokter'"]
    ]
];
$relatedData = [];
foreach ($relationships[$currentTable] ?? [] as $column => $relationInfo) {
    $relatedData[$column] = fetchRelatedData($dbh, $relationInfo);
}

function renderTableOptions() {
    $tables = ['pasien', 'kelurahan', 'paramedis', 'unit_kerja', 'periksa'];
    $currentTable = $_GET['table'] ?? 'pasien';
    return array_reduce($tables, fn($html, $table) => $html . "<option value=\"$table\"" . ($table === $currentTable ? ' selected' : '') . ">" . ucfirst($table) . "</option>", '');
}

function getFieldLabel($field) {
    $labels = [
        'id' => 'ID', 'nama' => 'Nama', 'tmp_lahir' => 'Tempat Lahir', 'tgl_lahir' => 'Tanggal Lahir',
        'gender' => 'Jenis Kelamin', 'email' => 'Email', 'alamat' => 'Alamat', 'kelurahan_id' => 'Kelurahan',
        'kategori' => 'Kategori', 'telpon' => 'Telepon', 'unit_kerja_id' => 'Unit Kerja', 'tanggal' => 'Tanggal Periksa',
        'berat' => 'Berat (kg)', 'tinggi' => 'Tinggi (cm)', 'tensi' => 'Tensi', 'keterangan' => 'Keterangan',
        'pasien_id' => 'Pasien', 'dokter_id' => 'Dokter', 'kec_id' => 'Kecamatan'
    ];
    return $labels[$field] ?? ucfirst($field);
}

function getRelatedDisplayValue($dbh, $table, $column, $id) {
    global $relationships;
    if (isset($relationships[$table][$column])) {
        $relationInfo = $relationships[$table][$column];
        $stmt = $dbh->prepare("SELECT {$relationInfo['display']} FROM {$relationInfo['table']} WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetchColumn() ?: 'N/A';
    }
    return $id;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Puskesmas CRUD System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        body {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(28, 56, 121, 0.6)), url('img/oyiza.jpg');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            font-family: 'Poppins', sans-serif;
        }
        .kawaii-button {
            background-color: #ff6f61;
            color: white;
            border-radius: 12px;
            padding: 10px 20px;
            transition: background-color 0.3s;
        }
        .kawaii-button:hover {
            background-color: #d45d5a;
        }
        .kawaii-input {
            border-radius: 12px;
            border: 2px solid #ff6f61;
            padding: 10px 10px 10px 40px;
            transition: border-color 0.3s;
        }
        .kawaii-input:focus {
            border-color: #d45d5a;
            outline: none;
        }
        .kawaii-table th, .kawaii-table td {
            border: 2px solid #ff6f61;
        }
        .kawaii-table th {
            background-color: #ffe4e1;
        }
        .icon-spacing {
            margin-right: 0.5rem;
        }
        .sidebar {
            transition: transform 0.3s ease-in-out;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Sidebar Toggle Button (Mobile Only) -->
    <button id="sidebarToggle" class="fixed top-4 left-4 z-50 md:hidden bg-[#213555] text-white p-2 rounded-md">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar fixed inset-y-0 left-0 z-40 w-64 bg-[#213555] shadow-lg md:translate-x-0">
        <div class="h-full flex flex-col">
            <!-- Sidebar Header -->
            <div class="px-6 py-4 border-b border-gray-700">
                <div class="flex items-center space-x-2 p-4">
                    <img src="img/oryza-cantik.jpg" alt="Logo" class="w-10 h-auto object-contain rounded-md">
                    <span class="font-bold text-white">Puskesmas Dashboard</span>
                </div>
            </div>

            <!-- Sidebar Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <?php
                // Define navigation items
                $navItems = [
                    ['url' => 'app.php?table=pasien', 'text' => 'Pasien', 'icon' => 'fa-user'],
                    ['url' => 'app.php?table=kelurahan', 'text' => 'Kelurahan', 'icon' => 'fa-building'],
                    ['url' => 'app.php?table=paramedis', 'text' => 'Paramedis', 'icon' => 'fa-user-md'],
                    ['url' => 'app.php?table=unit_kerja', 'text' => 'Unit Kerja', 'icon' => 'fa-hospital'],
                    ['url' => 'app.php?table=periksa', 'text' => 'Periksa', 'icon' => 'fa-stethoscope'],
                ];

                // Determine current page for active state
                foreach ($navItems as $item) {
                    $isActive = (strpos($item['url'], $currentTable) !== false);
                    $activeClass = $isActive 
                        ? 'text-white bg-[#78B3CE]' 
                        : 'text-white hover:bg-[#78B3CE] hover:text-white';
                    
                    echo '<a href="' . $item['url'] . '" class="flex items-center px-4 py-3 ' . $activeClass . ' rounded-lg transition duration-200">';
                    echo '<i class="fas ' . $item['icon'] . ' w-6"></i>';
                    echo '<span>' . $item['text'] . '</span>';
                    echo '</a>';
                }
                ?>
            </nav>

            <!-- Sidebar Footer -->
            <div class="px-4 py-6 border-t border-gray-700">
                <a href="logout.php" class="flex items-center px-4 py-3 text-white hover:bg-[#78B3CE] rounded-lg w-full transition duration-200">
                    <i class="fas fa-sign-out-alt w-6"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="md:ml-64 min-h-screen p-4">
        <div class="container mx-auto py-8 max-w-6xl">
            <header class="mb-6 mt-6 md:mt-0">
                <h1 class="text-3xl font-bold text-pink-400">Sistem Informasi Puskesmas</h1>
                <p class="text-yellow-400 mt-2">Oryza Ayunda Putri</p>
            </header>

            <!-- Messages -->
            <?php if (isset($_GET['msg'])): ?>
                <div id="successAlert" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-md shadow-sm transition-opacity duration-500" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle icon-spacing"></i>
                        <p>Record successfully <?= htmlspecialchars($_GET['msg']); ?>!</p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-md shadow-sm" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle icon-spacing"></i>
                        <p><?= htmlspecialchars($error); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Form Section -->
            <div class="bg-white p-6 rounded-xl shadow-md mb-6">
                <h2 class="text-xl font-semibold mb-4 pb-2 border-b border-gray-200 flex items-center">
                    <i class="fas <?= $editId ? 'fa-edit' : 'fa-plus-circle'; ?> icon-spacing"></i>
                    <?= $editId ? 'Edit' : 'Add New'; ?> <?= ucfirst($currentTable); ?>
                </h2>

                <form method="POST" action="app.php" class="space-y-4">
                    <input type="hidden" name="table" value="<?= htmlspecialchars($currentTable); ?>">
                    <input type="hidden" name="action" value="<?= $editId ? 'update' : 'add'; ?>">
                    <?php if ($editId): ?>
                        <input type="hidden" name="id" value="<?= htmlspecialchars($editId); ?>">
                    <?php endif; ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php foreach ($tableStructure as $column): ?>
                            <?php
                            if ($column['Field'] === 'id' && !$editId) continue;
                            $value = $editData[$column['Field']] ?? '';
                            $isRequired = $column['Null'] === 'NO' && $column['Extra'] !== 'auto_increment';
                            $fieldType = strpos($column['Type'], 'date') !== false ? 'date' : 'text';
                            $enumValues = null;
                            if (strpos($column['Type'], 'enum') !== false) {
                                preg_match("/enum\('(.+?)'\)/", $column['Type'], $matches);
                                if (isset($matches[1])) {
                                    $enumValues = explode("','", $matches[1]);
                                }
                            }
                            ?>

                            <div class="form-group">
                                <label for="<?= $column['Field']; ?>" class="block text-sm font-medium text-gray-700 mb-1">
                                    <?= getFieldLabel($column['Field']); ?><?= $isRequired ? ' <span class="text-red-500">*</span>' : ''; ?>
                                </label>

                                <?php if (isset($relationships[$currentTable][$column['Field']])): ?>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-500">
                                            <i class="fas fa-link"></i>
                                        </div>
                                        <select name="<?= $column['Field']; ?>" id="<?= $column['Field']; ?>" class="kawaii-input w-full" <?= $isRequired ? 'required' : ''; ?>>
                                            <option value="">-- Select <?= getFieldLabel($column['Field']); ?> --</option>
                                            <?php foreach ($relatedData[$column['Field']] as $item): ?>
                                                <option value="<?= $item['id']; ?>" <?= ($value == $item['id']) ? 'selected' : ''; ?>>
                                                    <?= htmlspecialchars($item[$relationships[$currentTable][$column['Field']]['display']]); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                <?php elseif (isset($enumValues)): ?>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-500">
                                            <i class="fas fa-list-ul"></i>
                                        </div>
                                        <select name="<?= $column['Field']; ?>" id="<?= $column['Field']; ?>" class="kawaii-input w-full" <?= $isRequired ? 'required' : ''; ?>>
                                            <option value="">-- Select <?= getFieldLabel($column['Field']); ?> --</option>
                                            <?php foreach ($enumValues as $enumValue): ?>
                                                <option value="<?= $enumValue; ?>" <?= ($value === $enumValue) ? 'selected' : ''; ?>>
                                                    <?= ucfirst($enumValue); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                <?php elseif ($column['Field'] === 'gender'): ?>
                                    <div class="mt-1 flex items-center space-x-6">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="gender" value="L" <?= ($value === 'L') ? 'checked' : ''; ?> class="form-radio text-pink-600 h-5 w-5">
                                            <span class="ml-2">Laki-laki</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="gender" value="P" <?= ($value === 'P') ? 'checked' : ''; ?> class="form-radio text-pink-600 h-5 w-5">
                                            <span class="ml-2">Perempuan</span>
                                        </label>
                                    </div>

                                <?php elseif (in_array($column['Field'], ['alamat', 'keterangan'])): ?>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-500">
                                            <i class="fas <?= $column['Field'] === 'alamat' ? 'fa-map-marker-alt' : 'fa-comment'; ?>"></i>
                                        </div>
                                        <textarea name="<?= $column['Field']; ?>" id="<?= $column['Field']; ?>" rows="3" class="kawaii-input w-full" <?= $isRequired ? 'required' : ''; ?>><?= htmlspecialchars($value); ?></textarea>
                                    </div>

                                <?php elseif (strpos($column['Field'], 'email') !== false): ?>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-500">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <input type="email" name="<?= $column['Field']; ?>" id="<?= $column['Field']; ?>" value="<?= htmlspecialchars($value); ?>" class="kawaii-input w-full" <?= $isRequired ? 'required' : ''; ?> placeholder="email@example.com">
                                    </div>

                                <?php elseif (strpos($column['Field'], 'telpon') !== false || strpos($column['Field'], 'phone') !== false): ?>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-500">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <input type="tel" name="<?= $column['Field']; ?>" id="<?= $column['Field']; ?>" value="<?= htmlspecialchars($value); ?>" class="kawaii-input w-full" <?= $isRequired ? 'required' : ''; ?> placeholder="0812345678">
                                    </div>

                                <?php elseif ($fieldType === 'date'): ?>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-500">
                                            <i class="fas fa-calendar"></i>
                                        </div>
                                        <input type="date" name="<?= $column['Field']; ?>" id="<?= $column['Field']; ?>" value="<?= htmlspecialchars($value); ?>" class="kawaii-input w-full" <?= $isRequired ? 'required' : ''; ?>>
                                    </div>

                                <?php elseif (strpos($column['Field'], 'nama') !== false || strpos($column['Field'], 'name') !== false): ?>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-500">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <input type="text" name="<?= $column['Field']; ?>" id="<?= $column['Field']; ?>" value="<?= htmlspecialchars($value); ?>" class="kawaii-input w-full" <?= $isRequired ? 'required' : ''; ?> placeholder="Enter <?= strtolower(getFieldLabel($column['Field'])); ?>">
                                    </div>

                                <?php else: ?>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-500">
                                            <i class="fas fa-keyboard"></i>
                                        </div>
                                        <input type="<?= $fieldType; ?>" name="<?= $column['Field']; ?>" id="<?= $column['Field']; ?>" value="<?= htmlspecialchars($value); ?>" class="kawaii-input w-full" <?= $isRequired ? 'required' : ''; ?> placeholder="Enter <?= strtolower(getFieldLabel($column['Field'])); ?>">
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <button type="reset" class="kawaii-button">
                            <i class="fas fa-undo icon-spacing"></i> Reset
                        </button>
                        <button type="submit" class="kawaii-button">
                            <i class="fas <?= $editId ? 'fa-save' : 'fa-plus-circle'; ?> icon-spacing"></i> <?= $editId ? 'Update' : 'Save'; ?>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Data Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <h2 class="text-xl font-semibold p-4 bg-gray-50 border-b"><?= ucfirst($currentTable); ?> Data</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 kawaii-table">
                        <thead>
                            <tr>
                                <?php foreach ($tableStructure as $column): ?>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?= getFieldLabel($column['Field']); ?></th>
                                <?php endforeach; ?>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($tableData)): ?>
                                <tr>
                                    <td colspan="<?= count($tableStructure) + 1; ?>" class="px-4 py-4 text-center text-sm text-gray-500">No data available</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($tableData as $row): ?>
                                    <tr class="hover:bg-gray-50">
                                        <?php foreach ($tableStructure as $column): ?>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                <?= isset($relationships[$currentTable][$column['Field']]) && !empty($row[$column['Field']]) ? htmlspecialchars(getRelatedDisplayValue($dbh, $currentTable, $column['Field'], $row[$column['Field']])) : ($column['Field'] === 'gender' ? ($row[$column['Field']] === 'L' ? 'Laki-laki' : 'Perempuan') : htmlspecialchars($row[$column['Field']] ?? 'N/A')); ?>
                                            </td>
                                        <?php endforeach; ?>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="app.php?table=<?= $currentTable; ?>&edit=<?= $row['id']; ?>" class="text-pink-600 hover:text-pink-900 mr-3">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form method="POST" action="app.php" class="inline">
                                                <input type="hidden" name="table" value="<?= htmlspecialchars($currentTable); ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                                <button type="submit" onclick="return confirm('Are you sure you want to delete this record?');" class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            <footer class="mt-6 text-center text-gray-600 text-sm py-4">
                <p>&copy; 2025 Puskesmas Management System</p>
            </footer>
        </div>
    </main>

    <script>
        // Mobile sidebar toggle
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('open');
                });
            }
            
            // Close sidebar when clicking outside (for mobile)
            document.addEventListener('click', function(event) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnToggle = sidebarToggle.contains(event.target);
                
                if (!isClickInsideSidebar && !isClickOnToggle && window.innerWidth < 768 && sidebar.classList.contains('open')) {
                    sidebar.classList.remove('open');
                }
            });

            // Auto-hide success message
            const successAlert = document.getElementById('successAlert');
            if (successAlert) {
                setTimeout(() => {
                    successAlert.style.opacity = 0;
                    setTimeout(() => successAlert.remove(), 1000);
                }, 3000);
            }
        });
    </script>
</body>
</html>