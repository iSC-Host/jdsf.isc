
// Remember to invoke within jQuery(window).load(...)
// If you don't, Jcrop may not initialize properly
$("document").ready(function(){

jQuery('#profile_pic').Jcrop({
onChange: showPreview,
        onSelect: updateCoords,
        setSelect:   [{-$X1}, {-$Y1}, {-$X2}, {-$Y2}],
        aspectRatio: 1
        });
        var rx = 120 / {-$W};
        var ry = 120 / {-$H};
        jQuery('#avatar_preview').css({
            marginLeft: '-' + Math.round(rx * {-$X1}) + 'px',
            marginTop: '-' + Math.round(ry * {-$Y1}) + 'px',
            width:Math.round(rx * $("#profile_pic").width()) + 'px',
            height:Math.round(ry * $("#profile_pic").height()) + 'px'
        });
        $("#new_profile_pic").change(function(){
$("#new_profile_pic").fadeOut();
        $("#img_form").submit();
        });
});
// Our simple event handler, called from onChange and onSelect
// event handlers, as per the Jcrop invocation above
        function showPreview(coords){
        if (parseInt(coords.w) > 0){
        var rx = 100 / coords.w;
                var ry = 100 / coords.h;
                $('#avatar_preview').css({
        marginLeft: '-' + Math.round(rx * coords.x) + 'px',
                marginTop: '-' + Math.round(ry * coords.y) + 'px',
                width:Math.round(rx * $("#profile_pic").width()) + 'px',
                height:Math.round(ry * $("#profile_pic").height()) + 'px'
        });
        }
        }

function updateCoords(c){
$('#x').val(c.x);
        $('#y').val(c.y);
        $('#x2').val(c.x2);
        $('#y2').val(c.y2);
        $('#w').val(c.w);
        $('#h').val(c.h);
};