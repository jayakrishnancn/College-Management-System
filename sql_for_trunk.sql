TRUNCATE TABLE course;
TRUNCATE TABLE department;
TRUNCATE TABLE history;
TRUNCATE TABLE login;
TRUNCATE TABLE permission;
TRUNCATE TABLE setup;
TRUNCATE TABLE subject;
TRUNCATE TABLE userpermission;

INSERT INTO login (uid, email, password, salt, name) VALUES ('1', 'adminuser', 'a461112ff4223a02d4b62cf63330007024521059', '9QJAx4MSlrcE', 'Admin User');
INSERT INTO  permission (permissionid,groupname,prio) values(1,'admin',0);
INSERT INTO  permission (permissionid,groupname,prio) values(2,'principal',10);
INSERT INTO  permission (permissionid,groupname,prio) values(3,'hod',15);
INSERT INTO  permission (permissionid,groupname,prio) values(4,'staff_advisor',20);
INSERT INTO  permission (permissionid,groupname,prio) values(5,'teacher',25);
INSERT INTO  permission (permissionid,groupname,prio) values(6,'student',30);
INSERT INTO  permission (permissionid,groupname,prio) values(7,'parent',35);
INSERT INTO  userpermission (permissionid,uid) values(1,1);