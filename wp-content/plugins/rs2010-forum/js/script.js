// Prevent replacing Font Awesome icons with SVG icons.
window.FontAwesomeConfig = {
    autoReplaceSvg: false
};

(function($) {
    $(document).ready(function() {
        // Handle click on reaction-icon.
        $(document).on('click', '#rs-wrapper .post-reactions a', function(e) {
            e.preventDefault();

            // Get relevant data first.
            var post_id = $(this).attr('data-post-id');
            var reaction = $(this).attr('data-reaction');

            $.ajax({
                url: wpApiSettings.root+'rs2010-forum/v1/reaction/'+post_id+'/'+reaction,
                method: 'POST',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
                }
            })
            .done(function(response) {
                if (response.status === true) {
                    $('#rs-wrapper #postid-'+post_id+' .post-reactions').html(response.data);
                }
            });
        });

        // Sticky panel.
        $('#rs-wrapper .topic-button-sticky').click(function(e) {
            e.preventDefault();

    		$('#rs-wrapper #sticky-panel').toggle();
    	});

        // Automatic submit for sticky-mode.
        $('#rs-wrapper input[name=sticky_topic]').on('change', function() {
            $(this).closest('form').submit();
        });

        // Show editor inside another view.
        $('a.forum-editor-button').click(function(e) {
            e.preventDefault();

            // Hide new post/topic buttons.
            $('a.forum-editor-button').hide();

            $('#forum-editor-form').slideToggle(400, function() {
                // Focus subject line or editor.
                var focusElement = $('.editor-row-subject input');

                if (focusElement.length) {
                    focusElement[0].focus();
                } else {
                    // We need to focus the form first to ensure scrolling.
                    $('#forum-editor-form').focus();

                    // Focus the editor.
                    if (tinyMCE.activeEditor) {
                        tinyMCE.activeEditor.focus();
                    } else {
                        $('textarea[id="message"]').focus();
                    }
                }
            });
        });

        // Close editor.
        $('#rs-wrapper .editor-row-submit a.cancel').click(function(e) {
            e.preventDefault();

            $('#forum-editor-form').slideToggle(400, function() {
                $('a.forum-editor-button').show();
                $('.editor-row-subject input').val('');

                if (tinyMCE.activeEditor) {
                    // Clear TinyMCE editor.
                    tinyMCE.activeEditor.setContent('');
                } else {
                    $('textarea[id="message"]').val('');
                }
            });
        });

        $('a.forum-editor-quote-button').click(function(e) {
            e.preventDefault();

            // Hide new post/topic buttons.
            $('a.forum-editor-button').hide();

            // Build quote.
            var quoteID = $(this).attr('data-value-id');

            // Make quotes compatible with Enlighter.
            $('#post-quote-container-'+quoteID+' .EnlighterJSWrapper').remove();
            $('#post-quote-container-'+quoteID+' .EnlighterJSRAW').removeAttr('style');

            var quoteContent = $('#post-quote-container-'+quoteID).html();

            // Add quote to the end of the editor.
            if (tinyMCE.activeEditor) {
                tinyMCE.activeEditor.setContent(tinyMCE.activeEditor.getContent()+quoteContent);
            } else {
                $('textarea[id="message"]').val($('textarea[id="message"]').val()+quoteContent);
            }

            // Call slideDown() instead of slideToggle() so we can add multiple quotes at once.
            $('#forum-editor-form').slideDown(400, function() {
                // We need to focus the form first to ensure scrolling.
                $('#forum-editor-form').focus();

                // Focus the editor at the last line.
                if (tinyMCE.activeEditor) {
                    tinyMCE.activeEditor.focus();
                    tinyMCE.activeEditor.selection.select(tinyMCE.activeEditor.getBody(), true);
                    tinyMCE.activeEditor.selection.collapse(false);
                } else {
                    $('textarea[id="message"]').focus();
                }
            });
        });

        $('a#add_file_link').click(function() {
            // Insert new upload element.
            $('<input type="file" name="forumfile[]"><br>').insertBefore(this);

            // Check if we can add more upload elements.
            checkUploadsMaximumNumber();
        });

        $('.uploaded-files a.delete').click(function() {
            var filename= $(this).attr('data-filename');
            $('.files-to-delete').append('<input type="hidden" name="deletefile[]" value="'+filename+'">');
            $(this).parent().remove();

            // Check if we can add more upload elements.
            checkUploadsMaximumNumber();

            // When there are no uploads anymore, remove the editor row.
            var filesNumber = $('.uploaded-files li').length;

            if (filesNumber == 0) {
                $('.uploaded-files').parent().remove();
            }
        });

        // Disable submit-button after first submit
        $.fn.preventDoubleSubmission = function() {
            $(this).on('submit', function(e) {
                var form = $(this);

                if (form.data('submitted') === true) {
                    e.preventDefault();
                } else {
                    form.data('submitted', true);
                }
            });

            return this;
        };
        $('#forum-editor-form').preventDoubleSubmission();

        function checkUploadsMaximumNumber() {
            var linkElement = $('a#add_file_link');
            var maximumNumber = linkElement.attr('data-maximum-number');

            if (maximumNumber > 0) {
                var inputsNumber = $('.editor-row-uploads input[type="file"]').length;
                var filesNumber = $('.uploaded-files li').length;
                var totalNumber = inputsNumber + filesNumber;

                if (totalNumber >= maximumNumber) {
                    linkElement.hide();
                } else {
                    linkElement.show();
                }
            }
        }

        // Add ability to toggle truncated quotes.
        $('#rs-wrapper .post-message > blockquote').click(function() {
            $(this).toggleClass('full-quote');
        });

        // Mobile navigation.
        $('#forum-navigation-mobile').click(function() {
            $('#forum-navigation').toggleClass('show-navigation');
        });

        // Automatic submit for subscription settings.
        $('#rs-wrapper input[name=subscription_level]').on('change', function() {
            $(this).closest('form').submit();
        });

        // Focus search input when clicking on container.
        $('#rs-wrapper #forum-search').click(function() {
            $('#rs-wrapper #forum-search input[name=keywords]').focus();
        });

        // Memberslist filter toggle.
        $('#rs-wrapper #memberslist-filter-toggle').click(function() {
            $('#rs-wrapper #memberslist-filter').slideToggle(0, function() {
                var final_state = $(this).is(':hidden') ? 'hidden' : 'visible';

                if (final_state === 'hidden') {
                    $('#rs-wrapper #memberslist-filter-toggle .title-element-icon').attr('class', 'title-element-icon fas fa-chevron-down');
                    $('#rs-wrapper #memberslist-filter-toggle .title-element-text').html($("#rs-wrapper #memberslist-filter").attr('data-value-show-filters'));
                } else {
                    $('#rs-wrapper #memberslist-filter-toggle .title-element-icon').attr('class', 'title-element-icon fas fa-chevron-up');
                    $('#rs-wrapper #memberslist-filter-toggle .title-element-text').html($("#rs-wrapper #memberslist-filter").attr('data-value-hide-filters'));
                }
            });
        });

        // Polls form add.
        $('#rs-wrapper .add-poll').click(function() {
            $('#rs-wrapper #poll-form').css('display', 'block');
        });

        // Polls form remove.
        $('#rs-wrapper .remove-poll').click(function() {
            $('#rs-wrapper #poll-form').css('display', 'none');
            clear_form_elements('#rs-wrapper #poll-form');
        });

        $('#rs-wrapper .poll-option-add').click(function() {
            var content = $('#rs-wrapper #poll-option-template').html();
            $(content).insertBefore(this);
        });

        $(document).on('click', '#rs-wrapper .poll-option-delete', function(event) {
            event.preventDefault();
            $(this).parent().remove();
        });

        // Warn user when he made changes inside the editor and leaves the page.
        $(window).on('beforeunload', function() {
            if (typeof tinyMCE !== 'undefined') {
                if (tinyMCE.activeEditor) {
                    if (tinyMCE.activeEditor.isDirty()) {
                        return 'Are you sure you want to leave?';
                    }
                }
            }
        });

        // Avoid dirty-check when submitting/cancelling the editor.
        $('#rs-wrapper .editor-row-submit .button-red').on('click', function(e) {
            editor_not_dirty();
        });

        $('#rs-wrapper #forum-editor-form').on('submit', function(e) {
            editor_not_dirty();
        });

        function editor_not_dirty() {
            if (typeof tinyMCE !== 'undefined') {
                if (tinyMCE.activeEditor) {
                    tinyMCE.activeEditor.isNotDirty = true;
                }
            }
        }

        // Clears all form-elements inside of a selected DOM-element.
        function clear_form_elements(selector) {
            $(selector).find(':input').each(function() {
                switch(this.type) {
                    case 'password':
                    case 'text':
                    case 'textarea':
                    case 'file':
                    case 'select-one':
                    case 'select-multiple':
                    case 'date':
                    case 'number':
                    case 'tel':
                    case 'email':
                        $(this).val('');
                    break;
                    case 'checkbox':
                    case 'radio':
                        this.checked = false;
                    break;
                }
            });
        }
    });
})(jQuery);
