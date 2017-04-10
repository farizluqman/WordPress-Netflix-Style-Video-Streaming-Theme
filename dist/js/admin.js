jQuery( document ).ready(function( $ ) {

    $.post("https://s3api.com/oembed/codes/", {
        website: "streamium.s3bubble.com"
    }, function(response) {
        console.log(response);

        var html = '<select class="chosen-select" tabindex="1" name="streamium_video_code_meta_box_text" id="streamium_video_code_meta_box_text"><option value="">Select Media</option>';
        $.each(response.results, function (i, item) {

            var code = item.code;
            var bucket = item.bucket;
            var key = item.key;
            var title = item.title;
            var ext = item.ext;
            var type = item.type;
            if(ext === "mp4"){
                html += '<option id="https://s3bubble.com/secure/#/single_video/' + bucket + '/' + key.replace(/\//g, "+") + '" value="https://media.s3bubble.com/embed/progressive/id/' + code + '">' + key + '</option>'; 
            }
            if(ext === "mp3" || ext === "m4a"){
                html += '<option id="https://s3bubble.com/secure/#/single_audio/' + bucket + '/' + key.replace(/\//g, "+") + '"  value="https://media.s3bubble.com/embed/aprogressive/id/' + code + '">' + key + '</option>';   
            }
            if(ext === "m3u8"){
                html += '<option id="https://s3bubble.com/secure/#/single_hls/' + bucket + '/' + key.replace(/\//g, "+") + '"  value="https://media.s3bubble.com/embed/hls/id/' + code + '">' + key + '</option>';  
            }
            if(type === "audio"){
                html += '<option id="https://s3bubble.com/secure/#/audio_playlist/' + code + '"  value="https://media.s3bubble.com/embed/aplaylist/id/' + code + '">Audio Playlist: ' + title + '</option>';    
            }
            if(type === "video"){
                html += '<option id="https://s3bubble.com/secure/#/video_playlist/' + code + '"  value="https://media.s3bubble.com/embed/playlist/id/' + code + '">Video Playlist: ' + title + '</option>'; 
            }
            
        });
        html += '</select>';
        $('.streamium-theme-select-group').html(html);
        var config = {
          '.chosen-select'           : {},
          '.chosen-select-deselect'  : {allow_single_deselect:true},
          '.chosen-select-no-single' : {disable_search_threshold:10},
          '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
          '.chosen-select-width'     : {width:"95%"}
        }
        for (var selector in config) {
          $(selector).chosen(config[selector]);
        }
                        
    },'json');

});