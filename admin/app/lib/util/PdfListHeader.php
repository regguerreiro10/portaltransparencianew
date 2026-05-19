<?php

class PdfListHeader
{
    public static function render($reportTitle = 'Relatorio gerencial')
    {
        $unitName = '';
        $document = '';

        try
        {
            $opened = false;
            if (!TTransaction::get())
            {
                TTransaction::open('minierp');
                $opened = true;
            }

            $unitId = TSession::getValue('idunit');
            if ($unitId)
            {
                $unit = new SystemUnit((int) $unitId);
                $unitName = $unit->name ?? '';
                $document = $unit->cnpj ?? '';
            }

            if ($opened)
            {
                TTransaction::close();
            }
        }
        catch (Exception $e)
        {
            if (!empty($opened) && TTransaction::get())
            {
                TTransaction::rollback();
            }
        }

        $title = self::formatTitle($reportTitle);
        $logo = self::logoSrc();

        return "
        <div class='pdf-list-header'>
            <table>
                <tr>
                    <td style='width: 42px;'>
                        <img src='{$logo}' class='pdf-list-logo'>
                    </td>
                    <td>
                        <div class='pdf-list-unit'>{$unitName}</div>
                        <div class='pdf-list-document'>{$document}</div>
                    </td>
                    <td class='pdf-list-title'>{$title}</td>
                    <td class='pdf-list-meta'>
                        Hora: " . date('H:i:s') . "<br>
                        Data: " . date('d/m/Y') . "<br>
                        Pagina: <span class='pdf-page-number'></span>
                    </td>
                </tr>
            </table>
        </div>";
    }

    private static function formatTitle($title)
    {
        $title = (string) $title;
        $title = preg_replace('/List$/', '', $title);
        $title = preg_replace('/(?<!^)([A-Z])/', ' $1', $title);
        $title = trim($title);

        return htmlspecialchars($title ?: 'Relatorio gerencial', ENT_QUOTES, 'UTF-8');
    }

    private static function logoSrc()
    {
        $logo = 'app/images/logo.png';
        if (file_exists($logo))
        {
            return 'data:image/png;base64,' . base64_encode(file_get_contents($logo));
        }

        return '';
    }
}
