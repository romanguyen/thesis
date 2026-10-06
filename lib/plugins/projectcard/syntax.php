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
        $labels = $this->labelsForPage((string) $ID);

        switch ($data[0]) {
            case 'PROJECT':
                $renderer->doc .= $this->renderHeader(trim($data[1]), $labels);
                break;
            case 'PERIOD':
                $renderer->doc .= '<div class="reskin-project-period"><span>' . hsc($labels['period']) . '</span><span>' . hsc(trim($data[1])) . '</span></div>';
                break;
            case 'GOAL':
                $renderer->doc .= $this->renderSection($labels['goal']);
                break;
            case 'ENDGOAL':
                $renderer->doc .= '</section>';
                break;
            case 'TASKS':
                $renderer->doc .= $this->renderSection($labels['tasks']);
                break;
            case 'ENDTASKS':
                $renderer->doc .= '</section>';
                break;
            case 'LINKS':
                $renderer->doc .= $this->renderActions($data[1], $labels);
                break;
            case 'ENDPROJECT':
                $renderer->doc .= '</article>';
                break;
        }

        return true;
    }

    /** Labels follow the wiki page namespace, not the site's configured UI language. */
    private function labelsForPage(string $pageId): array
    {
        return strpos($pageId, 'en:') === 0
            ? ['project' => 'Project', 'period' => 'Period', 'goal' => 'Project goal', 'tasks' => 'MetaCentrum tasks', 'results' => 'Results', 'links' => 'Project links']
            : ['project' => 'Projekt', 'period' => 'Období', 'goal' => 'Cíl projektu', 'tasks' => 'Úkoly MetaCentra', 'results' => 'Výsledky', 'links' => 'Odkazy projektu'];
    }

    private function renderHeader(string $title, array $labels): string
    {
        $anchor = 'project-' . substr(sha1($title), 0, 12);
        return '<article class="reskin-project-card" aria-labelledby="' . $anchor . '">'
            . '<header class="reskin-project-header">'
            . '<span class="reskin-project-eyebrow">' . hsc($labels['project']) . '</span>'
            . '<h3 class="reskin-project-title" id="' . $anchor . '">' . hsc($title) . '</h3>'
            . '</header>';
    }

    private function renderSection(string $label): string
    {
        return '<section class="reskin-project-section"><h4>' . hsc($label) . '</h4>';
    }

    /** Keep the original two-slot order, optional empties, and HTTP(S)-only contract. */
    private function renderActions(string $value, array $labels): string
    {
        $links = [];
        foreach (array_pad(explode('|', $value, 2), 2, '') as $index => $url) {
            $url = trim($url);
            if (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('~^https?://~i', $url)) continue;
            $text = $index === 0 ? 'Info' : $labels['results'];
            $icon = $index === 0 ? 'bi-box-arrow-up-right' : 'bi-file-earmark-check';
            $links[] = '<a class="reskin-project-action" href="' . hsc($url) . '"><i class="bi ' . $icon . '" aria-hidden="true"></i><span>' . hsc($text) . '</span></a>';
        }
        return $links ? '<nav class="reskin-project-actions" aria-label="' . hsc($labels['links']) . '">' . implode('', $links) . '</nav>' : '';
    }
}
