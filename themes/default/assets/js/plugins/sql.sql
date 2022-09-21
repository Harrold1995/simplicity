( SELECT DISTINCT ( SELECT strStatus FROM User_User_XR uuxr WHERE 
( uuxr.intUserId1 = '1' AND uuxr.intUserId2 = u.intUserId ) ) AS strFriendStatus1,
 uuxro.strStatus AS strFriendStatus2, uuxr.intUserId2 AS intUserId, u.strUserName ,
 u.strGender, IF( u.dtmBirth != '0000-00-00', FLOOR(DATEDIFF(CURDATE(), 
u.dtmBirth) / 365.25) , '?') AS intAge, u.strCountry AS strCountryCode,
 c.strCountry AS strCountry, u.strAvatar, u.fltPoints, 
IF( o.intUserId IS NULL, 'offline', 'online' ) AS strOnline, 
IF ( u.strAvatar != '', CONCAT( 'avatars/60/', u.strAvatar ), 
CONCAT( 'images/avatar_', u.strGender, '_small.png' ) ) as strAvatar,     
IF ( u.strAvatar != '', CONCAT( 'avatars/150/', u.strAvatar ),     
CONCAT( 'images/avatar_', u.strGender, '.png' )) as strLargeAvatar,
 u.dtmLastLogin, u.dtmRegistered FROM User_User_XR uuxr, 
User u LEFT JOIN User_User_XR uuxro ON uuxro.intUserId2 = '1' 
AND uuxro.intUserId1 = u.intUserId
 LEFT JOIN Online o ON o.intUserId = u.intUserId 
LEFT JOIN Country c ON c.strCountryCode = u.strCountry 
WHERE u.intUserId = uuxr.intUserId2 AND ( uuxr.strStatus = 'confirmed' ) 
AND uuxr.intUserId1='1' ) 

