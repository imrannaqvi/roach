use roach;
/* root user */
INSERT INTO `user` (`default_role`, `username`, `email`, `password`, `status`) VALUES ('root', 'root', 'root@roach.org', md5('root'), 'active');
