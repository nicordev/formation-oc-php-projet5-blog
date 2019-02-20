-- Données

INSERT INTO bl_tag (tag_id, tag_name) VALUES
(null, 'Qui suis-je ?'),
(null, 'Pourquoi ce site ?'),
(null, 'Mes réalisations'),
(null, 'Actualités'),
(null, 'Développement'),
(null, 'Electronique'),
(null, 'Apprendre'),
(null, 'Intelligence artificielle');

INSERT INTO bl_category (cat_id, cat_name)
VALUES
(null, 'Blog'),
(null, 'A propos'),
(null, 'Portfolio');

INSERT INTO bl_category_tag (ct_category_id_fk, ct_tag_id_fk)
VALUES
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(2, 1),
(2, 2),
(3, 3);

INSERT INTO bl_role
VALUES
(null, 'member'),
(null, 'author'),
(null, 'editor'),
(null, 'moderator'),
(null, 'admin');

INSERT INTO bl_member (m_id, m_email, m_password, m_name, m_description)
VALUES
(null, 'mentor.validateur@benice.plz', '$2y$10$MUyFQplVCEYj44iA7jnUu.iZoMHYvKUycm6NR2WDBCMNalKJEc.Wu', 'Chantal Gique', 'I''m an awesome validator working for OpenClassrooms and I like raspberries. Oh yes and I have every roles on this website.'),
(null, 'jean.tenbien@yahoo.fr', '$2y$10$L0qq2VnymYIphczV0c1nveww8rTKPEAkmn3tX/uHtgFrptfdXWMd2', 'Jean Tenbien', 'Hi. I''m a simple member. I can just write comments to say how good is a post.'),
(null, 'sarah.croche@gmail.com', '$2y$10$N27TlsyNeOxbtFmYrzKF0OJJIsU1nBN1v4VDSjl5LcTAhQwHW/0qS', 'Sarah Croche', 'I''m an author. I can write posts and I''m awesome.'),
(null, 'jim.nastique@gmail.com', '$2y$10$KR6s/Cn.hoA4uvc9XtVlCuOiomlAsgarPzogv69nBysaMPM.gNwee', 'Jim Nastique', 'I''m an editor. I can edit posts but I can not write new ones. But I''m still awesome.'),
(null, 'larry.viere@gmail.com', '$2y$10$g8IjxXoDKXf6bhXqGPypOOG3ICZ.3qlr7n/d.cbHeXh2bEJUH.VTy', 'Larry Vière', 'Hello, I''m a moderator. I can approuved, edit or delete comments.'),
(null, 'paul.emploi@gmail.com', '$2y$10$qfkWgJGDaLSKEiGI4seGMuAR0R4Xdm8RTKpu6hdofjH1R5W07Bzia', 'Paul Emploi', 'Me? I''m an admin. I can just manage members.'),
(null, 'lenny.bards@gmail.com', '$2y$10$UyLhlo3DGXZniMGcbAEXqec183r3vxyPZOKqVxHrYoUAtj2X89xU2', 'Lenny Bards', 'Hey! I''m an awesome author and editor on this wonderful website!');

INSERT INTO bl_role_member (rm_member_id_fk, rm_role_id_fk)
VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(2, 1),
(3, 1),
(3, 2),
(4, 1),
(4, 3),
(5, 1),
(5, 4),
(6, 1),
(6, 5),
(7, 1),
(7, 2),
(7, 3);

INSERT INTO bl_post (p_id, p_author_id_fk, p_last_editor_id_fk, p_creation_date, p_last_modification_date, p_markdown, p_title, p_excerpt, p_content)
VALUES
(null, 1, null, '2018-09-24 12:15:35', null, 0, 'Article exemple', 'Un article du site.', '<h2>Un article.</h2><p>Tout simple.</p>'),
(null, 1, 4, '2019-01-05 16:42:12', '2019-02-10 23:54:10', 0, 'Article modifié', 'Un autre article du site.', '<h2>Un article.</h2><p>Tout simple.</p>');

INSERT INTO bl_post_tag (pt_post_id_fk, pt_tag_id_fk)
VALUES
(1, 4),
(2, 4);

INSERT INTO bl_comment (com_id, com_parent_id_fk, com_post_id_fk, com_author_id_fk, com_last_editor_id_fk, com_creation_date, com_last_modification_date, com_content, com_approved)
VALUES
(null, null, 1, 2, null, '2019-02-15 12:13:52', null, 'Un commentaire A.', 1),
(null, 1, 1, 3, null, '2019-02-15 12:13:52', null, 'Un commentaire B en réponse au commentaire A.', 1),
(null, 2, 1, 4, null, '2019-02-15 12:14:52', null, 'Un commentaire C en réponse au commentaire B.', 1),
(null, 1, 1, 5, null, '2019-02-15 12:16:52', null, 'Un commentaire D en réponse au commentaire A.', 1),
(null, null, 1, 5, null, '2019-02-15 12:17:52', null, 'Un commentaire E.', 1),
(null, null, 1, 5, null, '2019-02-15 12:17:52', null, 'Un commentaire en attente.', 0),
(null, null, 2, 2, null, '2019-02-15 12:13:52', null, 'Purée mais cet article est <strong>bidon !!!</strong>', 1),
(null, 1, 2, 3, null, '2019-02-15 12:13:52', null, 'Tu t''es fait avoir ! Tes balises ne sont pas passées !', 1),
(null, 2, 2, 4, null, '2019-02-15 12:14:52', null, 'Ah oui. C''est triste.', 1),
(null, 1, 2, 5, null, '2019-02-15 12:16:52', null, 'Au moins pas de risque de piratage via les commentaires.', 1),
(null, null, 2, 5, null, '2019-02-15 12:17:52', null, 'Cet article est un article d''exemple pour montrer à quel point le système de commentaires est génialissime.', 1),
(null, null, 2, 5, null, '2019-02-15 12:17:52', null, 'Un commentaire écrit par un troll. Supprimez le !', 0),
(null, null, 2, 5, null, '2019-02-15 12:17:52', null, 'Se komantère é bouré deu fotes. Modifiez le svp.', 0);