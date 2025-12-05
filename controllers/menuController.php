<?php
function menuController(PDO $pdo, string $sql, array $params = []): string
{
 try {
        $data = getDataDB($pdo, $sql, $params);
        
        // Если данных нет — возвращаем пустой шаблон
        if (empty($data)) {
            return renderTemplate('menu', []);
        }
        
        return renderTemplate('menu', [
            'links' => $data
        ]);
        
    } catch (Exception $e) {

        error_log('Ошибка при загрузке меню: ' . $e->getMessage());

        return renderTemplate('error', ['message' => 'Не удалось загрузить меню']);
    }
}