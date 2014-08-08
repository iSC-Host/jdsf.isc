function deletePost(forum_id, topic_id, post_id)
{
    apprise('{-$view_topic_delete_confirm}', {verify: true}, function(r){
        if(r)
        {
            location.href = 'forums.php?deletepost&fid='+forum_id+'&tid='+topic_id+'&pid='+post_id;
        }
    })
}