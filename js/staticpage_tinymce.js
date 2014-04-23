(function ($)
{
    Drupal.behaviors.staticpage_tinymce = {
        attach: function (context)
            {
                // make a list of all css files on the current page.
                // we need to tell tinymce about these so that it can 
                // load the same files within the editor (for better wysiwyg).
                var tinymce_css = '';
                jQuery('head').find('link').each(function() {
                    var href = jQuery(this).attr('href')
                    if (/.css$/.test(href)) {
                        if (tinymce_css != '') {
                            tinymce_css += ',' 
                        }
                        tinymce_css += href;
                    }
                });
                tinymce.init({
                      selector: 'textarea', 
                      height: 250, 
                      content_css: tinymce_css,
                      body_id: 'content',
                      plugins: "code contextmenu image link table",
                      contextmenu: "link image inserttable | cell row column deletetable",
                      file_browser_callback: RoxyFileBrowser,
                });
            } 
    };
   
    function RoxyFileBrowser(field_name, url, type, win) {
      var roxyFileman = '/vendor/roxy_fileman/fileman/index.html';
      if (roxyFileman.indexOf("?") < 0) {     
        roxyFileman += "?type=" + type;   
      }
      else {
        roxyFileman += "&type=" + type;
      }
      roxyFileman += '&input=' + field_name + '&value=' + document.getElementById(field_name).value;
      if(tinyMCE.activeEditor.settings.language){
        roxyFileman += '&langCode=' + tinyMCE.activeEditor.settings.language;
      }
      tinyMCE.activeEditor.windowManager.open({
         file: roxyFileman,
         title: 'Roxy Fileman',
         width: 850, 
         height: 650,
         resizable: "yes",
         plugins: "media",
         inline: "yes",
         close_previous: "no"  
      }, {     window: win,     input: field_name    });
      return false; 
    }






})(jQuery);
