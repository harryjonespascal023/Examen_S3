INSERT INTO type_user (type) VALUES ('admin'), ('user');

INSERT INTO users (nom, password_hash, type_id, creation) VALUES ('admin', '$2y$10$uOadHUBq6qVB.ZIr/7NAzOqbBw/tFqVdKTJaGDCgY8ZPXsToqVtYi', '1', NULL);