<?php
include "DB_connection.php";

echo "<h2>Diagnostic complet de la table notifications</h2>";

try {
    // 1. Vérifier si la table existe
    $check_table = $conn->query("SHOW TABLES LIKE 'notifications'");
    if ($check_table->rowCount() > 0) {
        echo "<p>✅ La table 'notifications' existe</p>";
    } else {
        echo "<p>❌ La table 'notifications' n'existe pas</p>";
        exit;
    }

    // 2. Afficher la structure
    echo "<h3>Structure de la table:</h3>";
    $structure = $conn->query("DESCRIBE notifications");
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Colonne</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $structure->fetch()) {
        echo "<tr>";
        echo "<td><strong>" . $row['Field'] . "</strong></td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // 3. Compter les enregistrements
    $count = $conn->query("SELECT COUNT(*) as total FROM notifications");
    $total = $count->fetch()['total'];
    echo "<h3>Nombre d'enregistrements: $total</h3>";

    // 4. Si il y a des enregistrements, les afficher
    if ($total > 0) {
        echo "<h3>Contenu de la table:</h3>";
        $data = $conn->query("SELECT * FROM notifications ORDER BY id DESC LIMIT 5");
        echo "<table border='1' style='border-collapse: collapse;'>";
        
        // En-têtes dynamiques
        $first = true;
        while ($row = $data->fetch()) {
            if ($first) {
                echo "<tr>";
                foreach ($row as $key => $value) {
                    if (!is_numeric($key)) {
                        echo "<th><strong>$key</strong></th>";
                    }
                }
                echo "</tr>";
                $first = false;
            }
            
            echo "<tr>";
            foreach ($row as $key => $value) {
                if (!is_numeric($key)) {
                    echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                }
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>⚠️ La table est vide</p>";
        
        // 5. Tester l'insertion d'un enregistrement de test
        echo "<h3>Test d'insertion:</h3>";
        try {
            // Essayer différentes structures possibles
            $test_structures = [
                "INSERT INTO notifications (message, recipient, type, date, is_read) VALUES (?, ?, ?, ?, ?)" => 
                    ["Test message", 1, "test", date('Y-m-d'), 0],
                "INSERT INTO notifications (user_id, username, email, reason, status) VALUES (?, ?, ?, ?, ?)" => 
                    [1, "testuser", "test@example.com", "test", "pending"],
                "INSERT INTO notifications (recipient, type, date) VALUES (?, ?, ?)" => 
                    [1, "test", date('Y-m-d')]
            ];
            
            foreach ($test_structures as $sql => $params) {
                try {
                    $stmt = $conn->prepare($sql);
                    $stmt->execute($params);
                    echo "✅ Structure trouvée: $sql<br>";
                    
                    // Supprimer le test
                    $conn->query("DELETE FROM notifications WHERE id = LAST_INSERT_ID()");
                    break;
                } catch (PDOException $e) {
                    echo "❌ Structure échouée: " . substr($sql, 0, 50) . "... - " . $e->getMessage() . "<br>";
                }
            }
        } catch (Exception $e) {
            echo "Erreur lors du test: " . $e->getMessage();
        }
    }

} catch (PDOException $e) {
    echo "<p>❌ Erreur PDO: " . $e->getMessage() . "</p>";
}
?>

<style>
table { margin: 10px 0; }
th, td { padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
</style>