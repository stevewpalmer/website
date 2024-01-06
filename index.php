<!DOCTYPE html>
<html lang="en">
<head>
    <title>Steve Palmer</title>
    <link href="spalmer.css" rel="stylesheet">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" /> 
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
</head>
<body>
    <div>
        <a href='/' class="header">
            <img class="logo" alt="The Thinker" src="images/title.jpg">
            <span class="title">Steve Palmer</span>
        </a>
    </div>
    <div class="clearfix"></div>
    <div class="main">
    <?php
        /* Build an array of all article filenames in the articles
         * folder and sort by creation date descending so that the
         * most recent articles are earlier in the list.
         */
        $articles = array();
        foreach (new DirectoryIterator('articles') as $file) {
           if ($file->isDot()) continue;
           $articles[$file->getCTime()][] = $file->getFilename();
        }
        krsort($articles);

        $articles = call_user_func_array('array_merge', $articles);

        /* The index into the array of articles may be a query
         * parameter. For example:
         * 
         * ?page=4
         * 
         * If not then we pick the first article. The first line
         * of the selected article is the title. If it ends with
         * the word 'draft' then it is a work-in-progress so we
         * skip to the next article. Obviously this assumes there
         * is at least one non-draft article in the articles
         * folder or the process just hangs.
         */
        $article_index = $_GET["page"];
        if ($article_index == "") {
            echo "<h1>Table of Contents</h1>";
            $para = "";
            for ($index = 0; $index < count($articles); $index++) {
                $article_name = "articles/" . $articles[$index];
                $file = file_get_contents($article_name);
                $lines = explode("\n", $file);
                $title = array_shift($lines);
                if (!str_ends_with($title, "draft")) {
                    echo "<p><a href='?page=" . $index. "'>" . $title . "</a></p>";
                }
            }
        } else {
            do {
                if ($article_index == "") {
                    $article_index = 0;
                }
                $today_article_name = "articles/" . $articles[$article_index];
                $file = file_get_contents($today_article_name);
                $lines = explode("\n", $file);
                $title = array_shift($lines);

                $article_index = $article_index + 1;
                if ($article_index >= sizeof($articles)) $article_index = 0;
            } while (str_ends_with($title, "draft"));
    
            /* Render the article by bundling every line into a paragraph
            * until we reach a blank line or the end of the article. At
            * the end we add a link to the next article that was 
            * calculated above.
            */
            echo "<h1>" . $title . "</h1>";
            $para = "";
            foreach ($lines as $line) {
                if (trim($line)) {
                    $para .= " " . $line;
                } elseif (trim($para))  {
                    echo "<p>" . trim($para) . "</p>";
                    $para = "";
                }
            }
            if (trim($para)) echo "<p>" . trim($para) . "</p>";
            echo "<a href='?page=" . $article_index . "'>Next Article</a>";
        }
        ?>
    </div>
</body>
</html>
