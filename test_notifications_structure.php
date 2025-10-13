<?php
include "DB_connection.php";

echo "<h2>Test de la structure de la table notifications</h2>";

// Test 1: Vérifier si la table existe
try {
    $check_table = $conn->query("SHOW TABLES LIKE 'notifications'");
    if ($check_table->rowCount() > 0) {
        echo "<p>✅ La table 'notifications' existe</p>";
        
        // Test 2: Voir la structure de la table
        echo "<h3>Structure de la table notifications:</h3>";
        $structure = $conn->query("DESCRIBE notifications");
        echo "<table border='1'>";
        echo "<tr><th>Colonne</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $structure->fetch()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Test 3: Compter les enregistrements
        $count = $conn->query("SELECT COUNT(*) as total FROM notifications");
        $total = $count->fetch()['total'];
        echo "<p>Nombre d'enregistrements: $total</p>";
        
        // Test 4: Afficher quelques enregistrements
        if ($total > 0) {
            echo "<h3>Premiers enregistrements:</h3>";
            $sample = $conn->query("SELECT * FROM notifications LIMIT 3");
            echo "<table border='1'>";
            $first = true;
            while ($row = $sample->fetch()) {
                if ($first) {
                    echo "<tr>";
                    foreach (array_keys($row) as $key) {
                        if (!is_numeric($key)) {
                            echo "<th>$key</th>";
                        }
                    }
                    echo "</tr>";
                    $first = false;
                }
                echo "<tr>";
                foreach ($row as $key => $value) {
                    if (!is_numeric($key)) {
                        echo "<td>" . htmlspecialchars($value) . "</td>";
                    }
                }
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } else {
        echo "<p>❌ La table 'notifications' n'existe pas</p>";
    }
} catch (PDOException $e) {
    echo "<p>❌ Erreur: " . $e->getMessage() . "</p>";
}

// Test 5: Vérifier la table users aussi
try {
    echo "<h3>Structure de la table users:</h3>";
    $structure = $conn->query("DESCRIBE users");
    echo "<table border='1'>";
    echo "<tr><th>Colonne</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $structure->fetch()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (PDOException $e) {
    echo "<p>❌ Erreur avec la table users: " . $e->getMessage() . "</p>";
}
?>