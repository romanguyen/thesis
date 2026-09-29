<?php

use dokuwiki\Extension\SyntaxPlugin;

/**
 * Block-level markers for editable project cards. Descriptions remain native wiki text.
 */
class syntax_plugin_projectcard extends SyntaxPlugin
{
    public function getType()
    {
        return 'substition';
    }

    public function getPType()
    {
        return 'block';
    }

    public function getSort()
    {
        return 155;
    }

    public function connectTo($mode)
    {
        $this->Lexer->addSpecialPattern('~~(?:PROJECT:[^~\r\n]+|PERIOD:[^~\r\n]+|GOAL|ENDGOAL|TASKS|ENDTASKS|LINKS:[^~\r\n]*|ENDPROJECT)~~', $mode, 'plugin_projectcard');
    }

    public function handle($match, $state, $pos, Doku_Handler $handler)
    {
        $content = substr($match, 2, -2);
        $parts = explode(':', $content, 2);
        return [strtoupper($parts[0]), $parts[1] ?? ''];
    }

    public function render($format, Doku_Renderer $renderer, $data)
    {
        if ($format !== 'xhtml') return false;

        global $ID;
        $english = strpos((string) $ID, 'en:') === 0;
        $label = static function ($cs, $en) use ($english) {
            return $english ? $en : $cs;
        };

        switch ($data[0]) {
            case 'PROJECT':
                $title = trim($data[1]);
                $anchor = 'project-' . substr(sha1($title), 0, 12);
                $renderer->doc .= '<article class="reskin-project-card" aria-labelledby="' . $anchor . '">';
                $renderer->doc .= '<header class="reskin-project-header">';
                $renderer->doc .= '<span class="reskin-project-eyebrow">' . hsc($label('Projekt', 'Project')) . '</span>';
                $renderer->doc .= '<h3 class="reskin-project-title" id="' . $anchor . '">' . hsc($title) . '</h3>';
                $renderer->doc .= '</header>';
                break;
            case 'PERIOD':
                $renderer->doc .= '<div class="reskin-project-period"><span>' . hsc($label('Období', 'Period')) . '</span><span>' . hsc(trim($data[1])) . '</span></div>';
                break;
            case 'GOAL':
                $renderer->doc .= '<section class="reskin-project-section"><h4>' . hsc($label('Cíl projektu', 'Project goal')) . '</h4>';
                break;
            case 'ENDGOAL':
                $renderer->doc .= '</section>';
                break;
            case 'TASKS':
                $renderer->doc .= '<section class="reskin-project-section"><h4>' . hsc($label('Úkoly MetaCentra', 'MetaCentrum tasks')) . '</h4>';
                break;
            case 'ENDTASKS':
                $renderer->doc .= '</section>';
                break;
            case 'LINKS':
                $urls = array_pad(explode('|', $data[1], 2), 2, '');
                $links = [];
                foreach ($urls as $index => $url) {
                    $url = trim($url);
                    if (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('~^https?://~i', $url)) continue;
                    $text = $index === 0 ? 'Info' : $label('Výsledky', 'Results');
                    $icon = $index === 0 ? 'bi-box-arrow-up-right' : 'bi-file-earmark-check';
                    $links[] = '<a class="reskin-project-action" href="' . hsc($url) . '"><i class="bi ' . $icon . '" aria-hidden="true"></i><span>' . hsc($text) . '</span></a>';
                }
                if ($links) $renderer->doc .= '<nav class="reskin-project-actions" aria-label="' . hsc($label('Odkazy projektu', 'Project links')) . '">' . implode('', $links) . '</nav>';
                break;
            case 'ENDPROJECT':
                $renderer->doc .= '</article>';
                break;
        }

        return true;
    }
}
