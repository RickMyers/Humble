<!--#include virtual="/pages/includes/header.html" -->
<!--#include virtual="/pages/includes/container.html" -->
{assign var=first_name value=$user->getFirstName()}
<table style="width: 80%; margin-left: auto; margin-right: auto">
    <tr>
        <td><a href="#" onclick="window.history.back()">Back</a></td>
        <td style="text-align: center"><a href="/pages/Main.htmls">Home</a></td>
        <td style="text-align: right"><a href="/pages/Installation.htmls" title="" >Next</a></td>
    </tr>
</table>
{if ($first_name)}
    <style type="text/css">
        .whats-new-article-desc { 
            margin-bottom: 16px; font-family: monospace; font-size: .9em; letter-spacing: 1px
        }
    </style>
    <div style="width: 800px; min-width: 500px; margin-left: auto; margin-right: auto">

        <form name='whats_new_article_form' id='whats_new_article_form' onsubmit='return false'>
            <fieldset><legend>New Article, Author: {$first_name} {$user->getLastName()}</legend>
                <input type='text' name='title' id='whats_new_article_title' value='' />
                <div class="whats-new-article-desc">Article Title</div>
                <input type='text' name='version' id='whats_new_article_version' value='{$app_version.framework}' />
                <div class="whats-new-article-desc">Applies to what version</div>
                <textarea name='article' id='whats_new_article_text'></textarea>
                <div class="whats-new-article-desc">Article Text</div>
                <input type='button' value=' Publish '  style="float: right"/>
                <input type='button' value=' Save ' />
            </fieldset>
        </form>
    </div>
{/if}
{foreach from=$articles->fetch() item=article}
<div style='width: 80%; margin-left: auto; margin-right: auto; text-align: justify'>
    <div class='humble-topic-header'>{$article.title}</div>
    <div>
        {$article.article}
    </div>


</div>
{/foreach}
<script type="text/javascript">
    (function () {
        new EasyEdits('/edits/blog/article','blog-article');
    })();
</script>
<!--#include virtual="/pages/includes/footer.html" -->