<?php

/*
------------------
Language: English
------------------
*/

$lang['global_error'] = 'global error!';
$lang['ok'] = 'okay';
$lang['cancel'] = 'cancel';
$lang['yes'] = 'yes';
$lang['no'] = 'no';
$lang['save'] = 'save';

$lang['friends_select_cu'] = 'In this Cunity';
/*
------------------
path: \admin\includes\settings_general.php
------------------
*/

$lang['settings_general_name_long'] = 'Name too long, maximum of 100 characters.';
$lang['settings_general_motto'] = 'Motto / slogan too long, maximum of 100 characters.';
$lang['settings_general_invalid_design'] = 'Invalid or faulty design selected.';
$lang['settings_general_invalid_age'] = 'Invalid age selected.';
$lang['settings_general_email_invalid'] = 'The e-mail address seems to be invalid.';
$lang['settings_general_save_success'] = 'Saving successful.';
$lang['settings_general_entry_not_saved'] = 'Your entries could not be saved.';
$lang['settings_general_error_style'] = 'Error accessing the style directory';

/*
------------------
path: \admin\includes\settings_profiles.php
------------------
*/

$lang['settings_profiles_name_invalid'] = 'Name invalid. Maximum 15, minimum 3 characters.';
$lang['settings_profiles_type_invalid'] = 'Type invalid.';
$lang['settings_profiles_standard'] = 'Default value is no defined value.';
$lang['settings_profiles_invalid_treatment'] = 'Treatment invalid.';
$lang['settings_profiles_save_success'] = 'Memory operation is successful.';
$lang['settings_profiles_entry_not_saved'] = 'Your entries could not be saved.';
$lang['settings_profile_delete'] = 'Delete successful';
$lang['settings_profile_required'] = 'Required + subsequent statement';

/*
------------------
path: \admin\index.php
------------------
*/

$lang['index_missing_rights'] = 'Lack of rights.';
$lang['indes_wrong_user'] = 'Incorrect user data.';
$lang['index_no_action'] = 'You haven&#39;t performed any actions for 30 minutes and were thus automatically logged off.';

/*
------------------
path: \admin\overview.php
------------------
*/

$lang['overview_subject_invalid'] = 'Subject invalid. There should be no line breaks, and the subject must be at least 2 characters long.';
$lang['overview_message_empty'] = 'Message empty.';
$lang['overview_preview'] = 'Preview is on';
$lang['overview_sent'] = '.';
$lang['overview_message_sent'] = 'The message was sent to&nbsp;';
$lang['overview_members'] = '&nbsp;members.';
$lang['overview_message_could'] = 'The message could <strong> not </strong> be delivered to';
$lang['overview_from'] = 'of';
$lang['overview_member_name'] = 'members';

/*
------------------
path: \admin\users.php
------------------
*/

$lang['users_invalid_user'] = 'There is no user by this name.';
$lang['users_do_you'] = 'Do you really want to delete user';
$lang['users_delete'] = '?';
$lang['users_delete_all'] = 'All data of that user will be<strong> irrevocably </ strong> deleted.';
$lang['users_forum_person'] = 'By default only posts of this user will remain in the forums in order to preserve the meaning of the posts.';
$lang['users_click_delete'] = 'Another click on "Delete" deletes that user irrevocably.';
$lang['users_user_deleted'] = 'User was successfully and unrecoverably deleted.';

/*
------------------
path: \classes\db.class.php
------------------
*/


$lang['class_no_database'] = 'No database connection';


/*
------------------
path: admin\styles\default\templates\menu_main.tpl
------------------
*/
$lang['admin_mainmenu_overview'] = 'Overview';
$lang['admin_mainmenu_opencunity'] = 'Open Cunity';
$lang['admin_mainmenu_users'] = 'Userlist';
$lang['admin_mainmenu_round_mail'] = 'Newsletter';
$lang['admin_mainmenu_settings'] = 'Settings';
$lang['admin_mainmenu_modules'] = 'Modules';
$lang['admin_mainmenu_registration'] = 'Registration';
$lang['admin_mainmenu_home'] = 'Back to page';

/*
------------------
path: admin\styles\default\templates\menu_sub.tpl
------------------
*/

$lang['admin_submenu_updates'] = 'Updates';
$lang['admin_submenu_opencunity_register'] = 'Register';
$lang['admin_submenu_statistics'] = 'Statistics';
$lang['admin_submenu_overview'] = 'Overview';
$lang['admin_submenu_new'] = 'New Newsletter';
$lang['admin_submenu_page_layout'] = 'Page Layout';
$lang['admin_submenu_general'] = 'General';
$lang['admin_submenu_gallery'] = 'Gallery';
$lang['admin_submenu_filesharing'] = 'Filesharing';
$lang['admin_submenu_system'] = 'System';
$lang['admin_submenu_fields'] = 'Fields';
$lang['admin_submenu_profile'] = 'Profile';
$lang['admin_submenu_user_list'] = 'User-List';
$lang['admin_submenu_user_rights'] = 'User-Rights';
$lang['admin_submenu_connect_cunity'] = 'Cunity-Server';
$lang['admin_submenu_language'] = 'Language';
$lang['admin_submenu_filespace'] = 'Filespace';
$lang['admin_submenu_terms'] = 'Terms/Cond.';
$lang['admin_submenu_smtp'] = 'Mailing';
$lang['admin_submenu_menu'] = 'Menu';
$lang['admin_submenu_opencunity_settings'] = 'Settings';
$lang['admin_submenu_chat'] = 'Chat';
$lang['admin_submenu_friends'] = 'Friends';

/*
------------------
Sub-Menu
------------------
*/

$lang['admin_pages_title_terms'] = 'Terms Headline';
$lang['admin_pages_title_privacy'] = 'Privacy Headline';
$lang['admin_pages_title_imprint'] = 'Imprint Headline';


/*
------------------
path: admin\style\default\templates\login.tpl.html
------------------
*/

$lang['admin_login_confirm'] = 'Please confirm your login:';
$lang['admin_login_email'] = 'E-Mail:';
$lang['admin_login_password'] = 'Password:';

/*
------------------
path: admin\style\default\templates\overall_header.tpl.html
------------------
*/
$lang['admin_overall_header_administration'] = 'Administration';

/*
------------------
path: admin\style\default\templates\overview_mail.tpl.html
------------------
*/


$lang['admin_round_mail_overview'] = 'Newsletter';
$lang['admin_round_mail_send_info'] = 'Here you can send an email <strong> to all </ strong> registered users. To send important information or newsletter weekly, however, this feature can be used safely.';
$lang['admin_round_mail_warning'] = '<strong>Important!</strong> You should be careful not to use them too often. Some users might understand this as SPAM.
To send important information or newsletter week, however, this feature can be used safely.:';
$lang['admin_round_mail_subject'] = 'Subject';
$lang['admin_round_mail_subject_explain'] = 'The subject of the email should be short and meaningful.';
$lang['admin_round_mail_msg'] = 'Message';
$lang['admin_round_mail_preview'] = 'Preview';
$lang['admin_round_mail_yes'] = 'Yes';
$lang['admin_round_mail_no'] = 'No';
$lang['admin_round_mail_send_explain'] = 'Sends you the email as a sort of preview. Set the value to No to ultimately send the email to <strong> all </ strong> users.';
$lang['admin_round_mail_allow_reply'] = 'Allow response';
$lang['admin_round_mail_sender'] = 'The email address in your profile is entered as the sender. Thus, responses are sent to this email to you.';
$lang['admin_round_mail_sender2'] = 'is entered as the sender. The answers to the newsletter run to empty.';
$lang['admin_round_mail_send'] = 'Send';
$lang['admin_round_mail_save'] = 'Save';
$lang['admin_round_mail_not_sent'] = 'not sent yet';
$lang['admin_round_mail_date'] = 'Date';
$lang['admin_round_mail_sent'] = 'Sent';
$lang['admin_round_mail_action'] = 'Action';
$lang['admin_round_mail_new'] = 'New Newsletter';
$lang['admin_round_mail_back_overview'] = 'Back to Overview';
$lang['admin_round_mail_confirm_back'] = 'Warning! All changes will be lost!';
$lang['admin_round_mail_no_mails'] = 'There are no Newsletter!';
$lang['admin_round_mail_deleted'] = 'Mail deleted!';
$lang['admin_round_mail_saved'] = 'Mail saved!';
$lang['admin_round_mail_confirm_del'] = 'Are you sure you want to delete this mail?';
$lang['admin_round_mail_close'] = 'Close';

/*
------------------
path: admin\style\default\templates\overview_stats.tpl.html
------------------
*/

$lang['admin_overview_new_cunity'] = 'There is a new version of Cunity!';
$lang['admin_overview_update'] = 'Update';
$lang['admin_overview_last_login'] = 'Your last login was on';
$lang['admin_overview_last_login_from_ip'] = 'from the IP-Address';
$lang['admin_overview_stats_overview'] = 'Overview - Statistics';
$lang['admin_overview_stats_user'] = 'Users';
$lang['admin_overview_stats_reg_users'] = 'Registered Users:';
$lang['admin_overview_stats_inactive_users'] = 'Inactive users:';
$lang['admin_overview_stats_restricted_users'] = 'Blocked users:';
$lang['admin_overview_stats_user_graph'] = 'User-graphics';
$lang['admin_overview_stats_profile_img'] = 'Profile pictures:';
$lang['admin_overview_stats_profile_size'] = 'Filesize of all profile images:';
$lang['admin_overview_stats_news'] = 'News';
$lang['admin_overview_stats_published_news'] = 'Public News:';
$lang['admin_overview_stats_unpublished_news'] = 'Unpublished News:';
$lang['admin_overview_stats_comments'] = 'Comments:';
$lang['admin_overview_stats_forum'] = 'Forum';
$lang['admin_overview_stats_threads'] = 'Opened threads:';
$lang['admin_overview_stats_posts'] = 'Posts:';
$lang['admin_overview_stats_forums'] = 'Forums:';
$lang['admin_overview_stats_gallery'] = 'Gallery:';
$lang['admin_overview_stats_gallery_images'] = 'Images:';
$lang['admin_overview_stats_gallery_images_size'] = 'Filesize of all images:';
$lang['admin_overview_stats_gallery_albums'] = 'Albums:';
$lang['admin_overview_stats_gallery_albums_average'] = 'Average filesize of an album:';
$lang['admin_overview_stats_not_activated_users'] = 'Not yet activated Users:';
$lang['admin_overview_stats_inactive_info'] = 'Users, who aren\'t active at your Cunity!';

/*
------------------
path: admin\style\default\templates\settings.tpl.html
------------------
*/


$lang['admin_settings_settings'] = 'Settings';
$lang['admin_settings_find_settings'] = 'Here are the settings to their website.';

/*
------------------
path: admin\style\default\templates\settings_general.tpl.html
------------------
*/


$lang['admin_settings_general_settings'] = 'Settings - General';
$lang['admin_settings_general_website'] = 'Website';
$lang['admin_settings_general_website_name'] = 'Website name';
$lang['admin_settings_general_website_expl'] = 'This website will carry the name in the title of the browser window as well as at the head of the page.';
$lang['admin_settings_general_example'] = 'Example:';
$lang['admin_settings_general_car_expl1'] = 'Auto-Forum';
$lang['admin_settings_general_slogan'] = 'Slogan / motto of the site';
$lang['admin_settings_general_text'] = 'This text will appear under the name of the website in the head.';
$lang['admin_settings_general_car_expl2'] = 'The community for car enthusiasts';
$lang['admin_settings_general_preview'] = 'Preview';
$lang['admin_settings_general_design_info'] = 'Here the appearance of the site is determined.';
$lang['admin_settings_general_design'] = 'Cunity-Theme';
$lang['admin_settings_general_admin_design_info'] = 'Here the appearance of the admin-panel is determined.';
$lang['admin_settings_general_admin_design'] = 'Admin-Panel-Theme';
$lang['admin_settings_general_register'] = 'Registration';
$lang['admin_settings_general_necessary'] = 'Necessary information';
$lang['admin_settings_general_minimum_age'] = 'Minimum age';
$lang['admin_settings_general_name'] = 'Full name';
$lang['admin_settings_general_address'] = 'Address';
$lang['admin_settings_general_tel'] = 'Phone';
$lang['admin_settings_general_fields'] = 'Custom Fields';
$lang['admin_settings_general_suit'] = 'Swimming trunks';
$lang['admin_settings_general_obligatory'] = 'This information <strong>must</ strong> be given at registration. Note: Less is sometimes more - Too many coercive statements sometimes repel users and are generally unnecessary.';
$lang['admin_settings_general_change'] = 'You can add custom fields for user profiles in the settings under';
$lang['admin_settings_general_profile'] = 'Profile options';
$lang['admin_settings_general_create'] = '. Create';
$lang['admin_settings_general_minimum_expl'] = 'If the registered users have to be at a certain age, this can be determined at this point. Note: The minimum age has to be activated!';
$lang['admin_settings_general_contact'] = 'Contact';
$lang['admin_settings_general_email'] = 'eMail';
$lang['admin_settings_general_admin_email'] = 'E-mail address of an administrator. This address is the sender of this website and sent e-mails appear simultaneously in case of errors or problems encountered with the specified page.';
$lang['admin_settings_general_save'] = 'Save';
$lang['admin_settings_general_reset'] = 'Reset';
$lang['admin_settings_general_design_switch'] = 'Allow users to switch the design.';
$lang['admin_settings_land_page'] = 'Settings - Page Layout';
$lang['admin_settings_land_page_edit'] = 'Edit Homepage';
$lang['admin_settings_land_page_info'] = 'Here you can edit the page for visitors, who aren\'t logged in, are able to see!';
$lang['admin_settings_header_edit'] = 'Edit header';
$lang['admin_settings_header_info'] = 'Here you can edit the header of your Cunity!';
$lang['settings_page_layout_done'] = 'Changes saved successfully!';
$lang['admin_settings_save'] = 'Save';
$lang['admin_settings_close'] = 'Close';
$lang['admin_settings_preview'] = 'Preview';
$lang['admin_settings_language'] = 'Settings - Language';
$lang['admin_settings_language_please_select'] = 'Please select your language!';
$lang['admin_settings_language_german'] = 'German';
$lang['admin_settings_language_english'] = 'English';


//mailing
$lang['admin_settings_smtp_host'] = 'SMTP-Host';
$lang['admin_settings_smtp_port'] = 'SMTP-Port';
$lang['admin_settings_smtp_username'] = 'SMTP username';
$lang['admin_settings_smtp_password'] = 'SMTP password';
$lang['admin_settings_smtp_sender_name'] = 'SMTP sender name';
$lang['admin_settings_smtp_sender_address'] = 'SMTP sender address';
$lang['admin_settings_mail_options'] = 'Mail options';
$lang['admin_settings_smtp_settings'] = 'E-Mail Settings';
$lang['admin_settings_smtp_mail_design'] = 'Mail design';
$lang['admin_settings_smtp_mail_header'] = 'Mail header';
$lang['admin_settings_smtp_mail_footer'] = 'Mail footer';
$lang['admin_settings_mail_php_function'] = 'PHP mail function';
$lang['admin_settings_mail_send_function'] = 'Sendmail';
$lang['admin_settings_smtp_function'] = 'SMTP mail';
$lang['admin_settings_smtp_is_authentication'] = 'SMTP authentication';
$lang['admin_settings_smtp_auth_on'] = 'true';
$lang['admin_settings_smtp_auth_off'] = 'false';
$lang['admin_settings_mail_test_object'] = 'Test email';
$lang['admin_settings_mail_test_msg'] = 'This is a test email';
$lang['admin_settings_mail_test_enter_email'] = 'Please enter  an email address';
$lang['admin_settings_mail_test_sent_success'] = 'Email sent successfully!';
$lang['admin_settings_mail_mail_note'] = 'Here you can change the mail settings and test them at the bottom. If you want to use a SSL-authentication with SMTP, you may have to use ssl://<servername>.';
$lang['admin_settings_mail_btn_send'] = 'Send test email';
$lang['admin_settings_mail_btn_submit'] = 'Save';

//Menu
$lang['admin_settings_menu'] = 'Settings - Menu';
$lang['admin_settings_menu_info'] = 'Here you can edit the Cunity-Menu. You are able to change the order of the entries, or add one to your own page or module. <br/><b>For more details click on the item</b>';
$lang['admin_settings_menu_add'] = 'Add Menu-Entry';
$lang['admin_settings_menu_target'] = 'Target';
$lang['admin_settings_menu_delete'] = 'Delete entry';
$lang['admin_settings_menu_no_del'] = 'For deleting this entry, you\'ll have to deactivate the module';


$lang['menu_administration'] = 'Administration';
$lang['menu_fileshare'] = 'Filesharing';
$lang['menu_forums'] = 'Forum';
$lang['menu_friends'] = 'My Friends';
$lang['menu_galleries'] = 'Gallery';
$lang['menu_imprint'] = 'Imprint';
$lang['menu_messages'] = 'Inbox';
$lang['menu_events'] = 'Events';
$lang['menu_news'] = 'News';
$lang['menu_pinboard'] = 'Newsfeed';
$lang['menu_profile'] = 'My Profile';
$lang['menu_members'] = 'Members';
$lang['menu_logout'] = 'Logout';
$lang['menu_account'] = 'Account';
$lang['menu_privacy'] = 'Privacy';
$lang['menu_terms'] = 'Terms';
$lang['menu_contact'] = 'Contact';
$lang['menu_register'] = 'Register';
$lang['menu_home'] = 'Homepage';


//Update
$lang['admin_update_info'] = '<b>Attention!</b>The update would probably replace files, you have edited for yourself! If you want to keep this files, please do a backup but probably Cunity will not work properly!';
$lang['admin_update_start'] = 'Start update';
$lang['admin_update_success'] = 'Update success!';
$lang['admin_update_no_update'] = 'There is no update available!';
$lang['admin_update_fileytpe_failed'] = 'The uploaded file is not a cunity update file!';
$lang['admin_update_no_method'] = 'There is no method for the automatic update! Please open the \'allow_url_fopen\' or enable sockets fot the automatic update! You can update your Cunity manually with downloading the update file from <a href="http://www.cunity.net/update/update.cu">http://www.cunity.net/update/update.cu</a> and upload it in thie following form!';
/*
------------------
path: admin\style\default\templates\settings_profiles.tpl.html
------------------
*/

//Registration-fields
$lang['admin_registration_fields_text'] = 'Textfield';
$lang['admin_registration_fields_checkbox'] = 'Checkbox';
$lang['admin_registration_fields_switch'] = 'Status';
$lang['admin_registration_fields_radio'] = 'Radio-Buttons';
$lang['admin_registration_fields_selection'] = 'Selection';
$lang['admin_registration_fields_name'] = 'Name';
$lang['admin_registration_fields_new_value'] = 'Add a new value';
$lang['admin_registration_fields_saved_changes'] = 'Changes saved successfully!';

//Registration-Profile
$lang['admin_registration_fields_fields'] = 'Registration - Fields';
$lang['admin_registration_fields_field'] = 'Fieldname';
$lang['admin_registration_fields_importance'] = 'Importance';
$lang['admin_registration_fields_mandatory'] = 'mandatory field';
$lang['admin_registration_fields_optional'] = 'Optional field';
$lang['admin_registration_fields_reset'] = 'Reset';
$lang['admin_registration_fields_add_field'] = 'Add a new field';
$lang['admin_registration_fields_del_marked'] = 'Delete marked';
$lang['admin_registration_fields_M_marked'] = 'Set marked to mandatory';
$lang['admin_registration_fields_O_marked'] = 'Set marked to optional';
$lang['admin_registration_fields_del_confirm'] = 'Are you sure you want to delete this field?';
$lang['admin_registration_fields_submit_confirm'] = 'Are you sure you want to do this?';
$lang['admin_registration_fields_delete'] = 'Delete this field';
$lang['admin_registration_fields_register_age'] = 'Minimum-Age for registration';
$lang['admin_registration_fields_select_age'] = 'Please enter an age!';
$lang['admin_registration_fields_save'] = 'Save';
$lang['admin_registration_fields_deactive'] = 'This field is not displayed in the registration';
$lang['admin_registration_fields_active'] = 'This field is displayed in the registration';
$lang['admin_registration_fields_deactivate'] = 'Do not display this field in registration';
$lang['admin_registration_fields_activate'] = 'Display this field in the registration';
$lang['admin_registration_fields_type'] = 'Type';
$lang['admin_registration_fields_value'] = 'Values';
$lang['admin_registration_fields_close'] = 'Close';

$lang['admin_registration_fields_password'] = 'Password';
$lang['admin_registration_fields_nickname'] = 'Nickname';
$lang['admin_registration_fields_email'] = 'E-Mail';
$lang['admin_registration_fields_firstname'] = 'Firstname';
$lang['admin_registration_fields_lastname'] = 'Lastname';
$lang['admin_registration_fields_birthday'] = 'Birthday';
$lang['admin_registration_fields_street'] = 'Street';
$lang['admin_registration_fields_plz'] = 'ZIP-Code';
$lang['admin_registration_fields_town'] = 'Town';
$lang['admin_registration_fields_tel1'] = 'Phone #1';
$lang['admin_registration_fields_tel2'] = 'Phone #2';
$lang['admin_registration_fields_mobile'] = 'Mobile';

//general
$lang['admin_registration_general_general'] = 'Registration - General';
$lang['admin_registration_general_info'] = 'You can change, who would be able to register. You have got three possibilities:';
$lang['admin_registration_general_everybody'] = 'Everybody';
$lang['admin_registration_general_activate'] = 'Closed';
$lang['admin_registration_general_code'] = 'Invitation';
$lang['admin_registration_general_everybody_info'] = 'Everybody is allowed to register.';
$lang['admin_registration_general_activate_info'] = 'Everybody is allowed to register, but the the new user must be activated by the administrator!';
$lang['admin_registration_general_code_info'] = 'Users can send invitation-codes to anybody. New users need a code from a registered user to register!';
$lang['admin_registration_general_min_age'] = 'Enter the minimum-age for registration!';
$lang['admin_registration_general_age'] = 'Age:';
$lang['admin_registration_save'] = 'Save';
/*
------------------
path: admin\style\default\templates\users.tpl.html
------------------
*/
$lang['admin_users_delete_do_you'] = 'Are you sure you want to delete';
$lang['admin_users_delete_sure'] = '?';
$lang['admin_users_no'] = 'No, I don\'t want to be notified!';
$lang['admin_users_yes'] = 'Yes, I want to be notified!';
$lang['admin_users_check_all'] = 'Select all';
$lang['admin_users_check_none'] = 'Select none';
$lang['admin_users_save'] = 'Save changes';
$lang['admin_users_user'] = 'Users';
$lang['admin_users_nickname'] = 'Nickname';
$lang['admin_users_email'] = 'E-Mail';
$lang['admin_users_reg_date'] = 'Register date';
$lang['admin_users_last_login'] = 'Last login';
$lang['admin_users_last_ip'] = 'Last IP';
$lang['admin_users_status'] = 'Status';
$lang['admin_users_space'] = 'Space';
$lang['admin_users_space_info'] = 'Set the maximum space for this each user!';
$lang['admin_users_action'] = 'Action';
$lang['admin_users_delete_user'] = 'Delete this user';
$lang['admin_users_active'] = 'active';
$lang['admin_users_inactive'] = 'inactive';
$lang['admin_users_admin'] = 'admin';
$lang['admin_users_owner'] = 'owner';
$lang['admin_users_blocked'] = 'blocked';
$lang['admin_users_delete_marked'] = 'Delete';
$lang['admin_users_mail_marked'] = 'send Mail';
$lang['admin_users_set_active_marked'] = 'Set status to active';
$lang['admin_users_set_inactive_marked'] = 'Set status to inactive';
$lang['admin_users_send_message'] = 'Send message';
$lang['admin_users_block_user'] = 'block this user';
$lang['admin_users_reblock_user'] = 'Unblock user';
$lang['admin_users_add_admin'] = 'set status to admin';
$lang['admin_users_del_admin'] = 'Remove admin privileges';
$lang['admin_users_confirm_delete_marked'] = 'Are you sure you want to delete these users? All posts remain!';
$lang['admin_users_admin_info'] = 'Are you sure you want to add this user as admin? This user will be able to control your network!';
$lang['admin_users_message'] = 'Send message';
$lang['admin_users_receiver'] = 'Receiver';
$lang['admin_users_subject'] = 'Subject';
$lang['admin_users_send'] = 'Send';
$lang['admin_users_abort'] = 'Abort';
$lang['admin_users_forum'] = 'Forum';
$lang['admin_users_gallery'] = 'Gallery';
$lang['admin_users_messaging'] = 'Messaging';
$lang['admin_users_friends'] = 'Friends';
$lang['admin_users_filesharing'] = 'Filesharing';
$lang['admin_users_disable_all'] = 'Set all modules to OFF';
$lang['admin_users_enable_all'] = 'Set all modules to ON';
$lang['admin_users_notify_info'] = 'Would you like to be notified when a new user registers?';
$lang['admin_users_real_name_info'] = 'Should the users use real names or nicknames?';
$lang['admin_users_real_names'] = 'Real Names';
$lang['admin_users_nicknames'] = 'Nicknames';
$lang['admin_users_activate'] = 'Activate User';
$lang['admin_users_not_activated'] = 'not yet activated users';
$lang['admin_users_all_users'] = 'All active users';
$lang['admin_users_def_space'] = 'Please enter the maximum-space for every user!';
/*
------------------
path: admin\style\default\templates\users_delete.tpl.html
------------------
*/


$lang['admin_users_delete_delete'] = 'User - Delete';
$lang['admin_users_delete_name'] = 'Username';
$lang['admin_users_delete_enter'] = 'Please enter the username of the user to delete.';
$lang['admin_users_delete_forum'] = 'Forum Posts Delete';
$lang['admin_users_delete_yes'] = 'Yes';
$lang['admin_users_delete_no'] = 'No';
$lang['admin_users_delete_option'] = 'Check this only with \'yes\' if you really want to delete <strong> all </ strong> contributions of this user! (Useful for SPAM)';

$lang['admin_modules_module'] = 'Module';
$lang['admin_modules_modules'] = 'Modules - Overview';
$lang['admin_modules_gallery'] = 'Modules - Gallery';
$lang['admin_modules_gallery_overlay_fade_duration'] = 'Overlay fade duration';
$lang['admin_modules_gallery_overlay_fade_duration_description'] = 'The overlay fade duration determines how long it takes until the browser window initially becomes darker [1-400 ms]';
$lang['admin_modules_gallery_resize_duration'] = 'Resize duration';
$lang['admin_modules_gallery_resize_duration_description'] = 'The resize duration determines how fast/slow images are resized [1-400 ms]';
$lang['admin_modules_gallery_image_fade_duration'] = 'Image fade duration';
$lang['admin_modules_gallery_image_fade_duration_description'] = 'The image fade duration determines how fast/slow the images fade from one to the other [1-400 ms]';
$lang['admin_modules_gallery_caption_animation_duration'] = 'Comment animation duration';
$lang['admin_modules_gallery_caption_animation_duration_description'] = 'The comment animation duration determines how slow the comments appear at the bottom of the image [1-400 ms]';
$lang['admin_modules_gallery_fast'] = 'Fast';
$lang['admin_modules_gallery_slow'] = 'Slow';
$lang['admin_modules_save'] = 'Save';
$lang['admin_modules_saved_changes'] = 'Changes saved successfully!';
$lang['admin_modules_fileshare_select_filetypes'] = 'Please select which filetypes can be uploaded';
$lang['admin_modules_fileshare_select_storage'] = 'Please select how much space each user has!';
$lang['admin_modules_forbidden'] = 'To protect against viruses, these file types blocked:';
$lang['admin_modules_fail'] = 'This module is shutted down! You can set it <a href="modules.php?c=overview">here</a> ON!';
$lang['admin_modules_filesharing'] = 'Modules - Filesharing';
$lang['admin_modules_filesharing_add_filetype'] = 'Add Filetype';
$lang['admin_modules_filesharing_filetypes_example'] = '(Example: write "jpg" for ".jpg" filetypes. Do not add any . or * before the filetype.)';
$lang['admin_modules_filesharing_add'] = 'Add';
$lang['admin_modules_filesharing_path'] = 'Change Filepath';
$lang['admin_modules_filesharing_path_info'] = 'This path is only for your file-sharing. It should be outside your Cunity-folder! Else you are risking your security!';
$lang['admin_modules_filesharing_path_rights_error'] = 'The rights are missing!';

$lang['admin_modules_all'] = 'All users of this Cunity';
$lang['admin_modules_with_friends'] = 'Friends only';
$lang['admin_modules_chat_with_header'] = 'With whom are the users allowed to chat?';
$lang['admin_modules_fileshare'] = 'Filesharing';
$lang['admin_modules_galleries'] = 'Gallery';
$lang['admin_modules_friends'] = 'Friends';
$lang['admin_modules_messages'] = 'Messages';
$lang['admin_modules_members'] = 'User-List';
$lang['admin_modules_forums'] = 'Forums';
$lang['admin_modules_pinboard'] = 'Pinboard';
$lang['admin_modules_on'] = 'On';
$lang['admin_modules_off'] = 'Off';
$lang['admin_modules_chat'] = 'Chat';
$lang['admin_modules_events'] = 'Events';

$lang['admin_modules_memberlist_error'] = 'Are you sure you want to deactivate the userlist? Note, that you have set the opion "All users are friends"!';
$lang['admin_modules_friends_info'] = '';
$lang['admin_modules_friends_headline'] = 'Modules - Friends';
$lang['admin_modules_friends_friends'] = 'Only with friendships';
$lang['admin_modules_friends_friends_info'] = 'The users are only able to communicate, if they are friends. <i>The Userlist can be activated!</i>';
$lang['admin_modules_friends_members'] = 'All users are friends';
$lang['admin_modules_friends_members_info'] = 'All Users are automatically friends, all can communicate with everyone in this cunity. <i>The Userlist have to be activated!</i>';
$lang['admin_modules_friends_none'] = 'No friendships';
$lang['admin_modules_friends_none_info'] = 'There are no friendships. The users can only communicate via private-message. <i>The Userlist can be activated!</i>';


$lang['admin_opencunity_not_connected'] = 'Your Cunity is not connected with the Open-Cunity-Network!';
$lang['admin_opencunity_connected'] = 'Your Cunity is connected with the Open-Cunity-Network';
$lang['admin_opencunity_info'] = 'With the Open-Cunity-Network your Cunity is connected with every Cunity in this network. This means that your users can be friends with users from another cunity. They are able to send messages, vivit the gallery or attending events. You have to enable full-names in you Cunity so that your users can be found much better!';
$lang['admin_opencunity_register'] = 'Register you Cunity NOW!';
$lang['admin_opencunity_registration'] = 'Registration';
$lang['admin_opencunity_register_info'] = 'If you want to register your Cunity, you will have to provide: Name of your cunity, Slogan of your cunity, country and the purpose. Also the e-mail adress of the admin and the domain (URL) of your Cunity is required. We need the E-Mail adress only for internal purpose.';
$lang['admin_opencunity_check_label'] = 'Yes, I\'m sure connecting my Cunity with the Open-Cunity-Network';
$lang['admin_opencunity_finish'] = 'Finish';
$lang['admin_opencunity_purpose'] = 'Purpose';
$lang['admin_opencunity_country'] = 'Country';
?>