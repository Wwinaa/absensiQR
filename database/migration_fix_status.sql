ALTER TABLE users ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active';
ALTER TABLE users ADD COLUMN role ENUM('admin', 'kepsek', 'guru', 'walikelas', 'siswa') NOT NULL DEFAULT 'siswa';

UPDATE users SET role = 'admin' WHERE role_id = 1;
UPDATE users SET role = 'kepsek' WHERE role_id = 2;
UPDATE users SET role = 'guru' WHERE role_id = 3;
UPDATE users SET role = 'walikelas' WHERE role_id = 4;
UPDATE users SET role = 'siswa' WHERE role_id = 5;

ALTER TABLE users DROP FOREIGN KEY users_ibfk_1;
ALTER TABLE users DROP COLUMN role_id;
