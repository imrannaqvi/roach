use roach;

/* root user */
INSERT INTO `user` (`default_role`, `username`, `email`, `password`, `status`) VALUES ('root', 'root', 'root@roach.org', md5('root'), 'active');
SELECT id INTO @ROOT_USER_ID FROM `user` WHERE username = 'root';

/*
# Organisation:
create some organisations.
*/
SET @ORG1_NAME = 'Microsoft';
SET @ORG2_NAME = 'Google';
SET @ORG3_NAME = 'Sun Micro Systems';
INSERT INTO organisation(`name`, `uoc`, `uolu`) VALUES (@ORG1_NAME, @ROOT_USER_ID, @ROOT_USER_ID);
SELECT id INTO @ORG1_ID FROM `organisation` WHERE name = @ORG1_NAME;
INSERT INTO organisation(`name`, `uoc`, `uolu`) VALUES (@ORG2_NAME, @ROOT_USER_ID, @ROOT_USER_ID);
SELECT id INTO @ORG2_ID FROM `organisation` WHERE name = @ORG2_NAME;
INSERT INTO organisation(`name`, `uoc`, `uolu`) VALUES (@ORG3_NAME, @ROOT_USER_ID, @ROOT_USER_ID);
SELECT id INTO @ORG3_ID FROM `organisation` WHERE name = @ORG3_NAME;

select @ORG1_ID, @ORG2_ID, @ORG3_ID;
/* 
# project:
create some projects
*/
SET @ORG1_PROJECT1_NAME = 'Windows Seven';

INSERT INTO `project`( `organisation_id`, `type`, `name`, `initial`, `uoc`, `uolu`)
VALUES ( @ORG1_ID, 'project', @ORG1_PROJECT1_NAME, 'WIN7', @ROOT_USER_ID, @ROOT_USER_ID);
SELECT id INTO @ORG1_PROJECT1_ID FROM `project` WHERE name = @ORG1_PROJECT1_NAME && organisation_id = @ORG1_ID;

select @ORG1_PROJECT1_ID;