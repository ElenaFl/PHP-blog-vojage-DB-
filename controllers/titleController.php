<?php

function titleController(PDO $pdo, string $sql, array $params): string
{
    try {
        $data = getDataDB($pdo, $sql, $params);
        
        // Если данных нет — возвращаем пустой шаблон
        if (empty($data)) {
            return renderTemplate('title', ['pageTitle' => 'Без заголовка']);
        }
       
        $pageTitle = $data[0]['pageTitle'] ?? 'Без заголовка';
        return renderTemplate('title', ['pageTitle' => $pageTitle]);
        
    } catch (Exception $e) {

        error_log('Ошибка в при загрузке заголовка: ' . $e->getMessage());

        return renderTemplate('error', ['message' => 'Не удалось загрузить заголовок']);
    }

}