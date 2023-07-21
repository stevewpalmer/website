<!DOCTYPE html>
<html lang="en">
<head>
    <title>Steve W. Palmer</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <link href="spalmer.css" rel="stylesheet">
</head>
<body>
    <div class="header">
        <img alt="Black and white picture of Cuttbridge Cottage" src="images/title.png" style="height:100px;float:left;">
        <span class="title" style="float:left;margin-left:20px;">Steven W. Palmer</span>
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
        ?>
    </div>
</body>
</html>
