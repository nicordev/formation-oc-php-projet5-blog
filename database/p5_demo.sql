
DROP TABLE IF EXISTS bl_role_member;
DROP TABLE IF EXISTS bl_category_tag;
DROP TABLE IF EXISTS bl_post_tag;

DROP TABLE IF EXISTS bl_comment;
DROP TABLE IF EXISTS bl_post;
DROP TABLE IF EXISTS bl_member;
DROP TABLE IF EXISTS bl_role;
DROP TABLE IF EXISTS bl_category;
DROP TABLE IF EXISTS bl_tag;

CREATE TABLE bl_role(
                        r_id INT UNSIGNED AUTO_INCREMENT,
                        r_name VARCHAR(100),

                        CONSTRAINT pk_r_id
                            PRIMARY KEY (r_id)
)
    ENGINE = InnoDB;

CREATE TABLE bl_member(
                          m_id INT UNSIGNED AUTO_INCREMENT,
                          m_email VARCHAR(100) NOT NULL UNIQUE,
                          m_password VARCHAR(100) NOT NULL,
                          m_name VARCHAR(100) NOT NULL UNIQUE,
                          m_description VARCHAR(1000),

                          CONSTRAINT pk_m_id
                              PRIMARY KEY (m_id)
)
    ENGINE = InnoDB;

CREATE TABLE bl_role_member(
                               rm_member_id_fk INT UNSIGNED,
                               rm_role_id_fk INT UNSIGNED,

                               CONSTRAINT pk_rm_member_id_rm_role_id
                                   PRIMARY KEY (rm_member_id_fk, rm_role_id_fk),

                               CONSTRAINT fk_rm_member_id_m_id
                                   FOREIGN KEY (rm_member_id_fk)
                                       REFERENCES bl_member(m_id)
                                       ON UPDATE CASCADE
                                       ON DELETE CASCADE,

                               CONSTRAINT fk_rm_role_id_r_id
                                   FOREIGN KEY (rm_role_id_fk)
                                       REFERENCES bl_role(r_id)
                                       ON UPDATE CASCADE
                                       ON DELETE CASCADE
)
    ENGINE = InnoDB;

CREATE TABLE bl_post(
                        p_id INT UNSIGNED AUTO_INCREMENT,
                        p_author_id_fk INT UNSIGNED,
                        p_last_editor_id_fk INT UNSIGNED,
                        p_creation_date DATETIME NOT NULL,
                        p_last_modification_date DATETIME,
                        p_markdown TINYINT(1) NOT NULL DEFAULT 0,
                        p_title VARCHAR(100) NOT NULL,
                        p_excerpt VARCHAR(300) NOT NULL,
                        p_content LONGTEXT NOT NULL,

                        CONSTRAINT pk_p_id
                            PRIMARY KEY (p_id),

                        CONSTRAINT fk_p_author_id_m_id
                            FOREIGN KEY (p_author_id_fk)
                                REFERENCES bl_member(m_id)
                                ON UPDATE CASCADE
                                ON DELETE SET NULL,

                        CONSTRAINT fk_p_last_editor_id_m_id
                            FOREIGN KEY (p_last_editor_id_fk)
                                REFERENCES bl_member(m_id)
                                ON UPDATE CASCADE
                                ON DELETE SET NULL
)
    ENGINE = InnoDB;

CREATE TABLE bl_tag(
                       tag_id INT UNSIGNED AUTO_INCREMENT,
                       tag_name VARCHAR(100),

                       CONSTRAINT pk_tag_id
                           PRIMARY KEY (tag_id)
)
    ENGINE = InnoDB;

CREATE TABLE bl_post_tag(
                            pt_post_id_fk INT UNSIGNED,
                            pt_tag_id_fk INT UNSIGNED,

                            CONSTRAINT pk_pt_post_id_pt_tag_id
                                PRIMARY KEY (pt_post_id_fk, pt_tag_id_fk),

                            CONSTRAINT fk_pt_post_id_p_id
                                FOREIGN KEY (pt_post_id_fk)
                                    REFERENCES bl_post(p_id)
                                    ON UPDATE CASCADE
                                    ON DELETE CASCADE,

                            CONSTRAINT fk_pt_tag_tag_id
                                FOREIGN KEY (pt_tag_id_fk)
                                    REFERENCES bl_tag(tag_id)
                                    ON UPDATE CASCADE
                                    ON DELETE CASCADE
)
    ENGINE = InnoDB;

CREATE TABLE bl_category(
                            cat_id INT UNSIGNED AUTO_INCREMENT,
                            cat_name VARCHAR(100),

                            CONSTRAINT pk_cat_id
                                PRIMARY KEY (cat_id)
)
    ENGINE = InnoDB;

CREATE TABLE bl_category_tag(
                                ct_category_id_fk INT UNSIGNED,
                                ct_tag_id_fk INT UNSIGNED,

                                CONSTRAINT pk_ct_category_id_ct_tag_id
                                    PRIMARY KEY (ct_category_id_fk, ct_tag_id_fk),

                                CONSTRAINT fk_ct_category_cat_id
                                    FOREIGN KEY (ct_category_id_fk)
                                        REFERENCES bl_category(cat_id)
                                        ON UPDATE CASCADE
                                        ON DELETE CASCADE,

                                CONSTRAINT fk_ct_tag_id_tag_id
                                    FOREIGN KEY (ct_tag_id_fk)
                                        REFERENCES bl_tag(tag_id)
                                        ON UPDATE CASCADE
                                        ON DELETE CASCADE
)
    ENGINE = InnoDB;

CREATE TABLE bl_comment(
                           com_id INT UNSIGNED AUTO_INCREMENT,
                           com_parent_id_fk INT UNSIGNED,
                           com_post_id_fk INT UNSIGNED NOT NULL,
                           com_author_id_fk INT UNSIGNED NOT NULL,
                           com_last_editor_id_fk INT UNSIGNED,
                           com_creation_date DATETIME NOT NULL,
                           com_last_modification_date DATETIME,
                           com_content TEXT NOT NULL,
                           com_approved TINYINT(1) NOT NULL DEFAULT 0,

                           CONSTRAINT pk_com_id
                               PRIMARY KEY (com_id),

                           CONSTRAINT fk_com_parent_id_com_id
                               FOREIGN KEY (com_parent_id_fk)
                                   REFERENCES bl_comment(com_id)
                                   ON UPDATE CASCADE
                                   ON DELETE CASCADE,

                           CONSTRAINT fk_com_author_id_m_id
                               FOREIGN KEY (com_author_id_fk)
                                   REFERENCES bl_member(m_id)
                                   ON UPDATE CASCADE
                                   ON DELETE CASCADE,

                           CONSTRAINT fk_com_post_id_p_id
                               FOREIGN KEY (com_post_id_fk)
                                   REFERENCES bl_post(p_id)
                                   ON UPDATE CASCADE
                                   ON DELETE CASCADE,

                           CONSTRAINT fk_com_last_editor_id_m_id
                               FOREIGN KEY (com_last_editor_id_fk)
                                   REFERENCES bl_member(m_id)
                                   ON UPDATE CASCADE
                                   ON DELETE CASCADE
)
    ENGINE = InnoDB;


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
(null, 1, 4, '2019-01-05 16:42:12', '2019-02-10 23:54:10', 0, 'Article modifié', 'Un autre article du site.', '<h2>Un article.</h2><p>Tout simple.</p>'),
(null, 1, null, '2019-02-18 14:20:12', null, 1, 'Article écrit en Markdown', 'Un article écrit en Markdown.', 'Cet article contient du **Markdown** et c''est bien *sympa*.'),
(null, 3, null, '2019-02-02 10:52:45', null, 0, 'En bref', 'Je suis Nicolas Renvoisé, 31 ans à l''heure où j''écris cet article, et je me forme au métier de développeur backend avec l''aide d''OpenClassrooms. Pourquoi ? Parce que j''ai découvert que le code, j''adore ça !!!', '<h2>Comment j''en suis arriv&eacute; l&agrave; ?</h2>
<p>Au d&eacute;part, j''ai fais 3 ans d''&eacute;tudes dans le domaine de l''eau et de l''environnement apr&egrave;s mon bac scientifique. A l''issue, je me suis engag&eacute; dans l''arm&eacute;e en tant que contr&ocirc;leur a&eacute;rien. Finalement, l''arm&eacute;e ce n''&eacute;tait pas aussi bien que ce que j''esp&eacute;rais et j''ai arr&ecirc;t&eacute; &agrave; la fin de mon contrat initial. J''ai alors int&eacute;gr&eacute; un bureau d''&eacute;tudes sp&eacute;cialis&eacute; dans l''eau et l''environnement o&ugrave; je me suis d&eacute;couvert une passion pour la programmation lorsque j''ai eu &agrave; faire des fichiers excel en VBA. Depuis ce jour, je n''arr&ecirc;te pas de coder !</p>
<p>Un ami programmeur m''a conseill&eacute; de suivre les cours du site du z&eacute;ro sur le langage C pour d&eacute;buter sur de bonnes bases. C''est alors que j''ai d&eacute;couvert que le site du z&eacute;ro &eacute;tait devenu OpenClassrooms et je m''y suis inscrit.</p>
<p>Apr&egrave;s le cours de Mathieu Nebra sur le C (un super cours, je le recommande !), j''ai enchain&eacute; sur les cours li&eacute;s au web, domaine qui m''intriguait beaucoup. Entre temps, j''en avais marre de mon travail au bureau d''&eacute;tudes (la bo&icirc;te &eacute;tait super, mais je n''aimais pas r&eacute;diger des rapports de 600 pages refoul&eacute;s pour un oui ou pour un non par l''administration...) et finalement j''ai d&eacute;cid&eacute; de partir pour me reconvertir dans le d&eacute;veloppement web en suivant la formation de d&eacute;veloppeur d''application PHP/Symfony d''OpenClassrooms.</p>
<h2>Pourquoi cette formation et pas une autre ?</h2>
<p>Une &eacute;cole Simplon s''&eacute;tait ouverte &agrave; 40 minutes de chez moi depuis 2 ans, mais rien n''indiquait que la formation gratuite qu''elle proposait aller &ecirc;tre reconduite. Du coup j''ai opt&eacute; pour OpenClassrooms en demandant un financement de la part de P&ocirc;le emploi, ce qui fut accept&eacute; ! Ouf ! Du coup non seulement j''ai l''avantage de suivre une formation reconnue par l''Etat, mais en plus je n''ai pas &agrave; me d&eacute;placer !</p>
<h2>Comment se passe cette formation ?</h2>
<p>Bien. Vraiment tr&egrave;s bien m&ecirc;me ! Je m''&eacute;clate tous les jours &agrave; programmer, d&egrave;s que je s&egrave;che je peux en parler soit avec mon mentor, soit avec les autres &eacute;tudiants d''OpenClassrooms. Je n''ai jamais aussi bien v&eacute;cu une formation.</p>'),
(null, 3, null, '2019-02-03 11:00:01', null, 1, 'Pourquoi ce site ?', 'Ce site présente mes réalisations web, mon CV et des articles sur divers thèmes. Je l''ai créé pendant ma formation de développeur backend (c''est le projet 5).', 'Voici quelques liens utiles pour naviguer sur le site :

* [Mon CV](/cv/cv.pdf)
* [Mes réalisations](/blog?category-id=3)
* [Le blog](/blog?category-id=1)'),
(null, 3, null, '2019-01-06 16:23:14', null, 0, 'Mes sites web perso', 'Les 2 sites que j''ai réalisés avant ma formation de développeur backend, juste à l''aide des cours gratuits d''OpenClassrooms. Le premier est un site sur le barefoot running et le second est un site pour s''orienter en balade.', '<h2>Les sites web</h2>

<div class="card-deck">
    <div class="card">
        <a class="card-body" href="https://sansgodasses.com">
            Mon site sur le barefoot running : sansgodasses.com
        </a>
    </div>
    <div class="card">
        <a class="card-body" href="https://carte.ovh">
            Mon site pour s''orienter en balade : carte.ovh
        </a>
    </div>
</div>

<h2>Pour info</h2>

<p>
    <a href="https://sansgodasses.com">sansgodasses.com</a> utilise une bibliothèque JavaScript pour traduire le Markdown.
</p>
<p>
    <a href="https://carte.ovh">carte.ovh</a> utilise l''API de Google Maps et celle du géoportail.
</p>'),
(null, 3, null, '2019-01-07 14:51:11', null, 0, 'Mes projets réalisés pendant la formation d''OpenClassrooms', 'Voici les liens vers les projets mis en ligne. Vous trouverez aussi les fichiers sources sur mon profil GitHub.', '<h2>Les projets en ligne</h2>

<div class="card-deck">
    <div class="card">
        <a class="card-body" href="https://formation-oc-php.sansgodasses.com/projet-2/">
            Intégrez un thème Wordpress pour un client
        </a>
    </div>
    <div class="card">
        <a class="card-body" href="https://formation-oc-php.sansgodasses.com/projet-3/">
            Analysez les besoins de votre client pour son Festival de films
        </a>
    </div>
    <div class="card">
        <a class="card-body" href="https://formation-oc-php.sansgodasses.com/projet-4/">
            Concevez la solution technique d''une application de restauration en ligne, Express Food (contient uniquement les diagrammes UML et le fichier .sql)
        </a>
    </div>
    <div class="card">
        <a class="card-body" href="/home">
            Créez votre premier blog en PHP (c''est ce site)
        </a>
    </div>
</div>

<h2>A venir...</h2>

<ul>
    <li>Développez de A à Z le site communautaire SnowTricks</li>
    <li>Créez un web service exposant une API</li>
    <li>Améliorez une application existante de ToDo & Co</li>
</ul>

<h2>La formation OpenClassrooms</h2>

<p>
    <a href="https://openclassrooms.com/fr/paths/59-developpeur-dapplication-php-symfony">Contenu de la formation</a>
</p>');

INSERT INTO bl_post_tag (pt_post_id_fk, pt_tag_id_fk)
VALUES
(1, 4),
(2, 4),
(3, 4),
(4, 1),
(5, 2),
(6, 3),
(7, 3);

INSERT INTO bl_comment (com_id, com_parent_id_fk, com_post_id_fk, com_author_id_fk, com_last_editor_id_fk, com_creation_date, com_last_modification_date, com_content, com_approved)
VALUES
(null, null, 1, 2, null, '2019-02-15 12:13:52', null, 'Un commentaire A.', 1),
(null, 1, 1, 3, null, '2019-02-15 12:13:52', null, 'Un commentaire B en réponse au commentaire A.', 1),
(null, 2, 1, 4, null, '2019-02-15 12:14:52', null, 'Un commentaire C en réponse au commentaire B.', 1),
(null, 1, 1, 5, null, '2019-02-15 12:16:52', null, 'Un commentaire D en réponse au commentaire A.', 1),
(null, null, 1, 5, null, '2019-02-15 12:17:52', null, 'Un commentaire E.', 1),
(null, null, 1, 5, null, '2019-02-15 12:17:52', null, 'Un commentaire en attente.', 0),
(null, null, 2, 2, null, '2019-02-15 12:13:52', null, 'Purée mais cet article est <strong>bidon !!!</strong>', 1),
(null, 7, 2, 3, null, '2019-02-15 12:13:52', null, 'Tu t''es fait avoir ! Tes balises ne sont pas passées !', 1),
(null, 8, 2, 4, null, '2019-02-15 12:14:52', null, 'Ah oui. C''est triste.', 1),
(null, 7, 2, 5, null, '2019-02-15 12:16:52', null, 'Au moins pas de risque de piratage via les commentaires.', 1),
(null, null, 2, 5, null, '2019-02-15 12:17:52', null, 'Cet article est un article d''exemple pour montrer à quel point le système de commentaires est génialissime.', 1),
(null, null, 2, 5, null, '2019-02-15 12:17:52', null, 'Un commentaire écrit par un troll. Supprimez le !', 0),
(null, null, 2, 5, null, '2019-02-15 12:17:52', null, 'Se komantère é bouré deu fotes. Modifiez le svp.', 0);