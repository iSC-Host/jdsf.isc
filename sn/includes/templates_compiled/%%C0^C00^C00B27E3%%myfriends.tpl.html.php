<?php /* Smarty version 2.6.26, created on 2014-03-11 21:22:22
         compiled from file:style/newcunity/templates/myfriends.tpl.html */ ?>
<script language="javascript" type="text/javascript">	$.ajaxSetup ({
		cache: false
	});

	$("document").ready(function(){
	    $("#searchFriend").Watermark("<?php echo $this->_tpl_vars['search_member']; ?>
");                	    

		$("#searchMember").live('click',function(){
			var el = this;
			<?php if ($this->_tpl_vars['cunityconnected'] == true): ?>
			if($("#searchOption").val()=="none")
				localSearch($("#searchFriend").val());
			else if($("#searchOption").val()=="open")
				openSearch($("#searchFriend").val());
			<?php else: ?>
				localSearch($("#searchFriend").val());
			<?php endif; ?>					                               
        })
        
        

        $(".main_list_wrap")
            .live('mouseover',function(){
                $(this).children(".main_list_photos").show();
            })

            .live('mouseout', function(){
                $(this).children(".main_list_photos").hide();
            })            
    });

	window.setTimeout(function() {
	   <?php if (SEARCH): ?>
	   $(document).ready(function() {
		   var data = '{"action":"instantSearch", "searchTerm": "<?php echo $this->_tpl_vars['Q']; ?>
"}';
            $.post( "controllers/ajaxFriendsController.php", {json_data: data}, function(data_back){
                if(data_back.status == 1){
                    $("#myFriends").html(data_back.membersFound);
                    $("#search_load").hide();
                    $("#SearchMembersOnPartner").removeAttr('disabled');
                    $("#searchFriend").removeAttr('disabled');
                    $("#selectPartnerCunities").removeAttr('disabled');                    
                    refreshButtons();
                    imgLoadCheck();
                }
            }, "json");
       })
	   <?php else: ?>	   
	   $(document).ready(function() {
			var data = '{"c":"myFriends", "userid":"<?php echo $this->_tpl_vars['USER']; ?>
"}';
			$.post("controllers/ajaxFriendsController.php", {json_data:data},
				function (data_back) {
					$("#myFriends").html(data_back.messages);
					if(data_back.navigation=="on")
        			    $("#divLoadMoreStatus").show();
                    else
        			    $("#divLoadMoreStatus").hide();
        			refreshButtons();
        			imgLoadCheck();
                }, "json");
		});	   	   		
		<?php endif; ?>
	}, 200);
	
function openSearch(term){
	prepareSearch();
	var data = '{"action":"openSearch", "searchTerm": "' + term + '"}';
    $.post( "controllers/ajaxFriendsController.php", {json_data: data}, function(data_back){
        if(data_back.status == 1){
            $("#myFriends").html(data_back.membersFound);
            successSearch();
            refreshButtons();
            imgLoadCheck();
        }
    }, "json");
}

function localSearch(term){
	prepareSearch();
	var data = '{"action":"instantSearch", "searchTerm": "' + term + '"}';
    $.post( "controllers/ajaxFriendsController.php", {json_data: data}, function(data_back){
        if(data_back.status == 1){
            $("#myFriends").html(data_back.membersFound);
            successSearch();
            refreshButtons();
        }
    }, "json");
}

function prepareSearch(){
	$("#search_load").show();
	$("#searchMember").button("disable");
	$("#searchFriend").attr('disabled','disabled');
	$("#searchOption").attr('disabled','disabled');
}

function successSearch(){
	$("#search_load").hide();
	$("#searchMember").button("enable");
	$("#searchFriend").removeAttr('disabled');
	$("#searchOption").removeAttr('disabled');
}

function refreshFriends(data){
    var data = '{"c":"myFriends","userid": "<?php echo $this->_tpl_vars['USER']; ?>
"}';
	$.post("controllers/ajaxFriendsController.php", {json_data:data},function (data_back) {
		$("#myFriends").html(data_back.messages);
		if(data_back.navigation=="on"){
		    $("#divLoadMoreStatus").show();
		}else{
		    $("#divLoadMoreStatus").hide();
		}
		refreshButtons();
		imgLoadCheck();
	}, "json");	
}

function removeUser(user){
	$("#friend-"+user).fadeOut(200,function(){
		$(this).remove();
	})
}

function addFriendButton(id,text){
	$("#friendButton-"+id).button("option","label",text);
	$("#friendButton-"+id).button("option","icons",{primary: 'ui-icon-clock'});
	$("#friendButton-"+id).attr("onclick","");
}

function loadAllFriends(){
    $("#loadMoreFriends").hide();
    $("#moreFriendsLoad").show();    
    var data = '{"c":"myFriends","userid": "<?php echo $this->_tpl_vars['USER']; ?>
", "option": "showALL"}';
	$.post("controllers/ajaxFriendsController.php", {json_data:data},
		function (data_back) {
			$("#myFriends").html(data_back.messages);
			$("#divLoadMoreStatus").hide();
			$("#loadMoreFriends").show();
            $("#moreFriendsLoad").hide();
            refreshButtons();
            imgLoadCheck();
		}, "json");
}</script>
<div class="bar">
	<div class="bar-sub-a"></div>
	<div class="bar-sub-b">
		<h1><?php echo $this->_tpl_vars['TITLE']; ?>
</h1>
	</div>
	<div class="bar-sub-c"></div>
</div>
<div class="box-main-a-1"></div>
<div class="box-main-a-2 options-a-2">
<?php if (OWN_FRIENDS): ?>
<button class="jui-button" onclick="location.href='friends.php?c=requests'" icon="ui-icon-help">
    <?php echo $this->_tpl_vars['friends_requests']; ?>
<?php echo $this->_tpl_vars['REQUESTS']; ?>

</button>
<?php endif; ?>
<button class="jui-button" onclick="location.href='friends.php?c=invite'" icon="ui-icon-mail-open">
    <?php echo $this->_tpl_vars['friends_invite']; ?>

</button>    
</div>   
<div class="box-main-a-3"></div>            
 
<div class="box-main-a-1" style="margin-top: 10px;"></div>
<div class="box-main-a-2 options-a-2">
<table style="margin: 10px 0px; width: auto; font-size: 12px;">
	<tr>
		<td width="155px"><input id="searchFriend" type="text" /></td>
		<td width="360px">
		<?php if ($this->_tpl_vars['cunityconnected'] == true): ?>
        <select id="searchOption" style="margin-right:3px">
		    <option value="none"><?php echo $this->_tpl_vars['friends_select_cu']; ?>
</option>
		    <option value="open">OpenCunity</option>
		</select>
		<?php endif; ?>
		<button id="searchMember" class="jui-button" icon="ui-icon-search"><?php echo $this->_tpl_vars['friends_search']; ?>
</button>		
		<img src="style/default/img/loading.gif" id="search_load" style="display: none;" />
        </td>
	</tr>
</table>
</div>
<div class="box-main-a-3"></div>


<div id="myFriends" class="sample2" style="margin-top: 10px;">
<div style="text-align: center;">
<img src="style/newcunity/img/load.gif" style="margin: 10px auto;" />
</div>
</div>
<div id="divLoadMoreStatus" align="center" style="display:none;">
	<img src="style/default/img/load_big.gif" id="moreFriendsLoad" style="display: none; margin: 5px;"/>
	<button id="loadMoreFriends" class="jui-button" onclick="loadAllFriends();" icon="ui-icon-plus" icon2="ui-icon-plus"><?php echo $this->_tpl_vars['friends_show_all']; ?>
</button>
</div>