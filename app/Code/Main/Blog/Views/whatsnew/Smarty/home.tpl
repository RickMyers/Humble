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
            margin-bottom: 16px; font-family: monospace; font-size: .85em; letter-spacing: 1px
        }
    </style>
    <div style='clear: both; width: 80%; overflow: hidden; margin-left: auto; margin-right: auto;'>
        <img src='/images/blog/expand.png' class='editor_toggle' style='cursor: pointer; height: 22px; float: left; margin-right: 10px' onclick='$("#whats_new_editor").slideToggle(); $(".editor_toggle").hide(); $("#collapse_editor_icon").show()' id='expand_editor_icon' />
        <img src='/images/blog/collapse.png' class='editor_toggle' style='cursor: pointer; height: 22px; float: left; margin-right: 10px; display: none' onclick='$("#whats_new_editor").slideToggle(); $(".editor_toggle").hide(); $("#expand_editor_icon").show()' id='collapse_editor_icon' />
        New Article Editor
    </div>
    <div style="width: 80%; min-width: 500px; margin-left: auto; margin-right: auto; display: none; font-size: .9em" id='whats_new_editor'>

        <form name='whats_new_article_form' id='whats_new_article_form' onsubmit='return false'>
            <input type="hidden" name="id" id="whats_new_article_id" value="" />
            <fieldset><legend>New Article, Author: {$first_name} {$user->getLastName()}</legend>
                <input type='text' name='title' id='whats_new_article_title' value='' />
                <div class="whats-new-article-desc">Article Title</div>
                <input type='text' name='version' id='whats_new_article_version' value='{$app_version.framework}' />
                <div class="whats-new-article-desc">Applies to what version</div>
                <textarea name='article' id='whats_new_article_text'></textarea>
                <div class="whats-new-article-desc">Article Text</div>
                <input type='button' value=' Publish ' id="whats_new_article_publish"  style="float: right"/>
                <input type='button' value=' Save ' id="whats_new_article_save" />
            </fieldset>
        </form>
    </div>
    <script type="text/javascript">
        (function () {
            new EasyEdits('/edits/blog/article','blog-article');
        })();
    </script>                
{/if}
<div class="pt-8 pb-8 w-full text-center font-bold text-lg border bg-red-300">
    Notice: This section of the site is closed for the time being.  The previous entries into the "whatsnew" section are removed and no new entries will be made until after Version 1.0 is released.
</div>

<div class="pt-8">
    The Humble Framework is currently going through a refactoring based on years of experience.<br /><br />
    
    The Humble framework was at the heart of a large application with thousands of users and servicing billions of requests over many years.  During that time
    many parts of Humble proved exemplary, but also some issues were discovered, mostly around application structure.<br /><br />
    Humble separates application code from the framework at the repository level, but in a developers application folder, that demarcation is not as clear as it could be, and
    occasionally developers would in an attempt to solve an application problem, they would modify framework code (Rule #1: DON'T HACK ON CORE).<br /><br />
    As I approach the "1.0" release of the framework, I am restructuring the framework so it is much more clear as to what code is in the developer domain, and what
    code is the responsiblity of the framework maintainer (i.e. <a href="https://humbleprogramming.com/pages/Rick.htmls" target="_BLANK">ME</a>).<br /><br />
    Overall, over the years that this framework was used in a Production environment, and just as an FYI the first alpha of this framework was out in 2008, Humble has 
    outshined any application framework that I have ever had experience with, and I think it will make an excellent addition to the web application development community.<br /><br />
  
    --Rick<br /><br />
      <hr />
</div> 
{foreach from=$articles->setActive('Y')->fetch() item=article}
<div style='width: 80%; margin-left: auto; margin-right: auto; text-align: justify'>
    <div class='humble-topic-header'>{$article.title}</div>
    <div>
        {$article.article}
    </div>


</div>
{/foreach}

<!--#include virtual="/pages/includes/footer.html" -->