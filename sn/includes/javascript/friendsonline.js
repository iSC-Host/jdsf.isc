function loadchat() {
    var data = '{"c":"loadonlinefriends"}';
    $.post("controllers/ajaxFriendsController.php", {json_data: data},
    function(data_back) {
        if (data_back.status == 1) {
            $("#friendsonline").html(data_back.onlinefriendslist);
            $("#friendsmore").html(data_back.friendsmore);
            $("#friendsonlinebutton").button("option", "label", data_back.friendscount);
            $("#changeOnlineItem").html(data_back.statusChange);
            if (data_back.online == false)
                $("#friendsonlinebutton").button('disable');
            else
                $("#friendsonlinebutton").button('enable');
            chatHeartbeat();
            refreshButtons();
        }
    }, "json")
            .error(function() {
        $("#friendsonlinebutton").button('option', {
            icons: {
                primary: 'ui-icon-alert'
            },
            label: "An error occurred!"
        });
    })
}

function showAllChat() {
    var data = '{"c":"loadonlinefriends","do":"all"}';
    $.post("controllers/ajaxFriendsController.php", {json_data: data},
    function(data_back) {
        $("#friendsonline").html(data_back.onlinefriendslist);
        $("#friendsmore").html(data_back.friendsmore);
        $("#friendsonlinebutton").button("option", "label", data_back.friendscount);
        $("#changeOnlineItem").html(data_back.statusChange);
        if (data_back.online == false)
            $("#friendsonlinebutton").button('disable');
        else
            $("#friendsonlinebutton").button('enable');
        chatHeartbeat();
        refreshButtons();
    }, "json");
}

function changeChatStatus() {
    var data = '{"c":"changeChatStatus"}';
    $.post("controllers/ajaxFriendsController.php", {json_data: data},
    function(data_back) {
        $("#chatmenu_dropdown").slideUp(100);
        loadchat();
    }, "json");
}

function removeRequest(userid, userData, remote, callback) {
    apprise("{-$friends_confirm_remove_request}", {verify: true}, function(r) {
        if (r) {
            var data = '{"c":"delete_friend", "userid": "' + userid + '","userData":"' + userData + '","remote":"' + remote + '"}';
            $.post("controllers/ajaxFriendsController.php", {json_data: data}, function(data_back) {
                if (data_back.status == 1)
                    if (callback != undefined && typeof callback == 'function')
                        callback(userid);
                    else
                        apprise(data.error);
            }, "json");
        }
    })
}

function addasfriend(userid, userData, remote, callback) {
    var data = '{"c":"get_add_text", "userid": "' + userid + '","userData":"' + userData + '","remote":"' + remote + '"}';
    $.post("controllers/ajaxFriendsController.php", {json_data: data}, function(data_back) {
        if (data_back.status == 1) {
            apprise(data_back.text, {confirm: true, textOk: '{-$friends_send_request}'}, function(a) {
                if (a) {
                    var data = '{"c":"add_friend", "userid": "' + userid + '","userData":"' + userData + '","remote":"' + remote + '"}';
                    $.post("controllers/ajaxFriendsController.php", {json_data: data}, function(data_back) {
                        if (data_back.status == 1)
                            if (callback != undefined && typeof callback == 'function')
                                callback();
                            else
                                apprise(data_back.error);
                    }, "json");
                }
            })
        }
    }, "json");
}

function respondRequest(userid, userData, remote, callback) {
    var data = '{"c":"get_request_text", "userid": "' + userid + '","userData":"' + userData + '","remote":"' + remote + '"}';
    $.post("controllers/ajaxFriendsController.php", {json_data: data}, function(data_back) {
        if (data_back.status == 1) {
            apprise(data_back.text, {confirm: true, textOk: '{-$friends_confirm_request}'}, function(a) {
                if (a) {
                    var data = '{"c":"confirm_request", "userid": "' + userid + '","userData":"' + userData + '","remote":"' + remote + '"}';
                    $.post("controllers/ajaxFriendsController.php", {json_data: data}, function(data_back) {
                        if (data_back.status == 1)
                            if (callback != undefined && typeof callback == 'function')
                                callback();
                            else
                                apprise(data_back.error);
                    }, "json");
                }
            })
        }
    }, "json");
}

function deleteFriend(userid, userData, remote, callback) {
    var data = '{"c":"get_delete_text", "userid": "' + userid + '","userData":"' + userData + '","remote":"' + remote + '"}';
    $.post("controllers/ajaxFriendsController.php", {json_data: data}, function(data_back) {
        if (data_back.status == 1) {
            apprise(data_back.text, {confirm: true, textOk: '{-$friends_confirm_delete}'}, function(a) {
                if (a) {
                    var data = '{"c":"delete_friend", "userid": "' + userid + '","userData":"' + userData + '","remote":"' + remote + '"}';
                    $.post("controllers/ajaxFriendsController.php", {json_data: data}, function(data_back) {
                        if (data_back.status == 1)
                            if (callback != undefined && typeof callback == 'function')
                                callback(userid, data_back.newText);
                            else
                                apprise(data_back.error);
                    }, "json");
                }
            })
        }
    }, "json");
}

function blockFriend(userid, userData, remote, callback) {
    var data = '{"c":"get_block_text", "userid": "' + userid + '","userData":"' + userData + '","remote":"' + remote + '"}';
    $.post("controllers/ajaxFriendsController.php", {json_data: data}, function(data_back) {
        apprise(data_back.text, {confirm: true, textOk: '{-$friends_block_friend}'}, function(a) {
            if (a) {
                var data = '{"c":"block_friend", "userid": "' + userid + '","userData":"' + userData + '","remote":"' + remote + '"}';
                $.post("controllers/ajaxFriendsController.php", {json_data: data}, function(data_back) {
                    if (data_back.status == 1)
                        if (callback != undefined && typeof callback == 'function')
                            callback(userid);
                        else
                            apprise(data.error);
                });
            }
        })
    }, "json")
}

function unblockFriend(userid, userData, remote, callback) {
    var data = '{"c":"get_unblock_text", "userid": "' + userid + '","userData":"' + userData + '","remote":"' + remote + '"}';
    $.post("controllers/ajaxFriendsController.php", {json_data: data}, function(data_back) {
        apprise(data_back.text, {confirm: true, textOk: '{-$friends_unblock_friend}'}, function(a) {
            if (a) {
                var data = '{"c":"delete_friend", "userid": "' + userid + '","userData":"' + userData + '","remote":"' + remote + '"}';
                $.post("controllers/ajaxFriendsController.php", {json_data: data}, function(data_back) {
                    if (data_back.status == 1)
                        if (callback != undefined && typeof callback == 'function')
                            callback(userid, data_back.newText);
                        else
                            apprise(data_back.error);
                }, "json");
            }
        })
    }, "json")
}

window.setTimeout('loadchat()', 500);
window.setInterval('loadchat()', {-$timeoutFriendsOnline});