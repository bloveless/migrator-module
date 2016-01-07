$(function() {

    if($('#file-upload-progress').length > 0) {

        var migratedFiles = [];
        var failedFiles = [];

        // diskSlug is coming from the view
        processNextFileSet(diskSlug, 0, 10);

        function processNextFileSet(diskSlug, lowerLimit, incCount) {

            $.get('/admin/migrator/migrate_files/' + lowerLimit + '/' + (lowerLimit + incCount), function(data) {
                var data = $.parseJSON(data);

                if(data['status'] && data['remaining'] > 0) {

                    migratedFiles = migratedFiles.concat(data['migrated']);
                    failedFiles = failedFiles.concat(data['failed']);

                    // filesCount is coming from the view
                    var newPercentage = (lowerLimit + (incCount * 2)) / filesCount * 100;
                    if(newPercentage > 100) { newPercentage = 100; }

                    var newText = lowerLimit + (incCount * 2);
                    if(newText > filesCount) { newText = filesCount; }

                    $('#current-count').text(lowerLimit + (incCount * 2));
                    $('#file-upload-progress .progress-bar').attr('aria-valuenow', newPercentage).css('width', newPercentage + '%');
                    $('#file-upload-progress .progress-bar .sr-only').text(newPercentage + "% Complete");

                    if(data['remaining'] > 0) {

                        processNextFileSet(diskSlug, (lowerLimit + incCount), incCount);

                    } else {

                        if(failedFiles.length > 0) {

                            var failedFilesHtml = "";
                            for(var i = 0; i < failedFiles.length; i++) {
                                failedFilesHtml += '<div style="border-bottom: 1px solid red; padding-bottom: 15px; margin-bottom: 15px;">' + failedFiles[i]['path'] + '<br>' + failedFiles[i]['filename'] + '</div>';
                            }

                            $('#failed-files').append("<h3>Failed Files</h3><p>You'll need to move these files over manually.</p>");
                            $('#failed-files').append(failedFilesHtml);

                        } else {

                            $('#failed-files').append("Congratulations! No files failed to migrate.");

                        }
                    }
                }
            });

        }
    }
});