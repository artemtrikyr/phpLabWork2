<?php
//Щоб завантажити дані із файлу
function load_azs_data($filename) {
    if (file_exists($filename)) {
        $data = file_get_contents($filename);
        return json_decode($data, true);
    }
    return [];
}

function save_azs_data($filename, $azs) {
    file_put_contents($filename, json_encode($azs));
}

$azs = load_azs_data('azs_data.json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $owner = $_POST['owner'];
    $required_fuel = intval($_POST['required_fuel']);
    $available_azs = [];

    foreach ($azs as $station) {
        if ($station['owner'] === $owner && $station['fuel_stock'] >= $required_fuel) {
            $available_azs[] = $station;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Перевірка наявності пального</title>
</head>
<body>
    <h1>АЗС</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Код</th>
                <th>Адреса</th>
                <th>Фірма-власник</th>
                <th>Запаси пального (літри)</th>
                <th>Ціна за літр</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($azs as $station): ?>
                <tr>
                    <td><?= htmlspecialchars($station['code']) ?></td>
                    <td><?= htmlspecialchars($station['address']) ?></td>
                    <td><?= htmlspecialchars($station['owner']) ?></td>
                    <td><?= htmlspecialchars($station['fuel_stock']) ?></td>
                    <td><?= htmlspecialchars($station['fuel_price']) ?> грн</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Перевірка наявності пального</h2>
    <form method="post">
        <label for="owner">Фірма-власник:</label>
        <input type="text" name="owner" required><br><br>

        <label for="required_fuel">Кількість літрів:</label>
        <input type="number" name="required_fuel" required><br><br>

        <input type="submit" value="Перевірити">
    </form>

    <?php if (isset($available_azs)): ?>
        <h2>АЗС, де є достатньо пального:</h2>
        <?php if (empty($available_azs)): ?>
            <p>Немає АЗС цього власника з достатньою кількістю пального.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($available_azs as $azs): ?>
                    <li>АЗС: <?= htmlspecialchars($azs['code']) ?>, Адреса: <?= htmlspecialchars($azs['address']) ?>, Запаси: <?= htmlspecialchars($azs['fuel_stock']) ?> літрів</li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>

<?php
save_azs_data('azs_data.json', $azs);
?>
