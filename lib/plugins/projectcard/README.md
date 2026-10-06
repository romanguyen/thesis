# Project cards

Use these block markers in a DokuWiki page. Text and lists between section markers remain ordinary DokuWiki syntax. Omit empty sections and the `LINKS` marker if no real destinations exist.

```text
~~PROJECT:MAGIC — Middleware for collaborative Applications and Global vIrtual Communities~~
~~PERIOD:květen 2015 – duben 2017~~
~~GOAL~~
Project description, with normal [[https://example.org|wiki links]] and lists.
~~ENDGOAL~~
~~TASKS~~
MetaCentrum's contribution.
~~ENDTASKS~~
~~LINKS:https://example.org/info|https://example.org/results~~
~~ENDPROJECT~~
```

The two `LINKS` slots are Info and Results, in that order. Leave a slot empty if there is no destination: `~~LINKS:|https://example.org/results~~`. Only HTTP(S) URLs are accepted. Period and section markers are optional. Always close each opened section and card. Keep markers on their own lines and leave a blank line before and after wiki lists.

Rendering helpers keep labels, header/section markup and action filtering separate. Labels follow the page's `en:` prefix (other pages retain Czech labels), independently of the site's UI language. Existing title-derived card anchors and all marker/parser contracts remain unchanged.
