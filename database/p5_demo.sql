
DROP TABLE IF EXISTS bl_role_member;
DROP TABLE IF EXISTS bl_post_category;
DROP TABLE IF EXISTS bl_post_tag;

DROP TABLE IF EXISTS bl_comment;
DROP TABLE IF EXISTS bl_post;
DROP TABLE IF EXISTS bl_member;
DROP TABLE IF EXISTS bl_role;
DROP TABLE IF EXISTS bl_category;
DROP TABLE IF EXISTS bl_tag;

DROP TABLE IF EXISTS bl_key;
DROP TABLE IF EXISTS bl_connection_try;

CREATE TABLE bl_connection_try(
                                  cot_id INT UNSIGNED AUTO_INCREMENT,
                                  cot_count INT UNSIGNED,
                                  cot_last_try DATETIME,
                                  cot_user VARCHAR(100),

                                  CONSTRAINT  pk_connection_try_id
                                      PRIMARY KEY (cot_id)
)
    ENGINE = InnoDB;

CREATE TABLE bl_key(
                       key_id INT UNSIGNED AUTO_INCREMENT,
                       key_value INT UNSIGNED NOT NULL,

                       CONSTRAINT pk_key_id
                           PRIMARY KEY (key_id)
)
    ENGINE = InnoDB;

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
                       tag_name VARCHAR(100) UNIQUE,

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
                            cat_name VARCHAR(100) UNIQUE,

                            CONSTRAINT pk_cat_id
                                PRIMARY KEY (cat_id)
)
    ENGINE = InnoDB;

CREATE TABLE bl_post_category(
                                 pc_post_id_fk INT UNSIGNED,
                                 pc_category_id_fk INT UNSIGNED,

                                 CONSTRAINT pk_pc_post_id_pc_category_id
                                     PRIMARY KEY (pc_post_id_fk, pc_category_id_fk),

                                 CONSTRAINT fk_pc_post_id_post_id
                                     FOREIGN KEY (pc_post_id_fk)
                                         REFERENCES bl_post(p_id)
                                         ON UPDATE CASCADE
                                         ON DELETE CASCADE,

                                 CONSTRAINT fk_pc_category_cat_id
                                     FOREIGN KEY (pc_category_id_fk)
                                         REFERENCES bl_category(cat_id)
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
(1, 'Ceci est une étiquette'),
(2, 'Article exemple'),
(3, 'Nicolas Renvoisé'),
(4, 'Sport'),
(5, 'Développement'),
(6, 'Electronique'),
(7, 'Apprendre'),
(8, 'Intelligence artificielle'),
(9, 'CSS'),
(10, 'PHP'),
(11, 'HTML'),
(12, 'Git'),
(13, 'GitHub'),
(14, 'Wordpress'),
(15, 'CMS');

INSERT INTO bl_category (cat_id, cat_name)
VALUES
(null, 'Blog'),
(null, 'A propos'),
(null, 'Portfolio');

INSERT INTO bl_role
VALUES
(null, 'member'),
(null, 'author'),
(null, 'editor'),
(null, 'moderator'),
(null, 'admin');

INSERT INTO bl_member (m_id, m_email, m_password, m_name, m_description)
VALUES
(null, 'mentor.validateur@benice.plz', '$2y$10$xJ.gG0a5hfd1FGBVwGDq0ODQIgAphJ3Slo4bC9sOyMlHAOJIM9kBq', 'Chantal Gique', 'I''m an awesome validator working for OpenClassrooms and I like raspberries. Oh yes and I have every roles on this website.'),
(null, 'jean.tenbien@yahoo.fr', '$2y$10$xJ.gG0a5hfd1FGBVwGDq0ODQIgAphJ3Slo4bC9sOyMlHAOJIM9kBq', 'Jean Tenbien', 'Hi. I''m a simple member. I can just write comments to say how good is a post.'),
(null, 'sarah.croche@gmail.com', '$2y$10$xJ.gG0a5hfd1FGBVwGDq0ODQIgAphJ3Slo4bC9sOyMlHAOJIM9kBq', 'Sarah Croche', 'I''m an author. I can write posts and I''m awesome.'),
(null, 'jim.nastique@gmail.com', '$2y$10$xJ.gG0a5hfd1FGBVwGDq0ODQIgAphJ3Slo4bC9sOyMlHAOJIM9kBq', 'Jim Nastique', 'I''m an editor. I can edit posts but I can not write new ones. But I''m still awesome.'),
(null, 'larry.viere@gmail.com', '$2y$10$xJ.gG0a5hfd1FGBVwGDq0ODQIgAphJ3Slo4bC9sOyMlHAOJIM9kBq', 'Larry Vière', 'Hello, I''m a moderator. I can approuved, edit or delete comments.'),
(null, 'paul.emploi@gmail.com', '$2y$10$xJ.gG0a5hfd1FGBVwGDq0ODQIgAphJ3Slo4bC9sOyMlHAOJIM9kBq', 'Paul Emploi', 'Me? I''m an admin. I can just manage members.'),
(null, 'lenny.bards@gmail.com', '$2y$10$xJ.gG0a5hfd1FGBVwGDq0ODQIgAphJ3Slo4bC9sOyMlHAOJIM9kBq', 'Lenny Bards', 'Hey! I''m an awesome author and editor on this wonderful website!');

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
(1, 1, null, '2018-09-24 12:15:35', '2018-09-24 12:15:35', 0, 'Article exemple', 'Un article du site.', '<h2>Un article.</h2><p>I can explain. It''s very valuable. Dear God, they''ll be killed on our doorstep! And there''s no trash pickup until January 3rd. And until then, I can never die? I didn''t ask for a completely reasonable excuse! I asked you to get busy!</p>
<p><strong>Our love isn''t any different from yours, except it''s hotter, because I''m involved.</strong> <em> It may comfort you to know that Fry''s death took only fifteen seconds, yet the pain was so intense, that it felt to him like fifteen years.</em> And it goes without saying, it caused him to empty his bowels.</p>
<h2>No, of course not. It was… uh… porno. Yeah, that''s it.</h2>
<p>Is the Space Pope reptilian!? You can crush me but you can''t crush my spirit! Yeah, and if you were the pope they''d be all, "Straighten your pope hat." And "Put on your good vestments." Leela, are you alright? You got wanged on the head.</p>
<ol>

    <li>Take me to your leader!</li><li>I''m sorry, guys. I never meant to hurt you. Just to destroy everything you ever believed in.</li><li>Yes, I saw. You were doing well, until everyone died.</li>

</ol>

<h3>I don''t want to be rescued.</h3>
<p>In your time, yes, but nowadays shut up! Besides, these are adult stemcells, harvested from perfectly healthy adults whom I killed for their stemcells. Ooh, name it after me! We''re rescuing ya. Oh sure! Blame the wizards!</p>
<ul>

    <li>Large bet on myself in round one.</li><li>They''re like sex, except I''m having them!</li><li>And then the battle''s not so bad?</li>

</ul>

<p>That''s right, baby. I ain''t your loverboy Flexo, the guy you love so much. You even love anyone pretending to be him! You''ll have all the Slurm you can drink when you''re partying with Slurms McKenzie! Now that the, uh, garbage ball is in space, Doctor, perhaps you can help me with my sexual inhibitions?</p>
<p>I didn''t ask for a completely reasonable excuse! I asked you to get busy! A true inspiration for the children. I''ve been there. My folks were always on me to groom myself and wear underpants. What am I, the pope?</p>
<p>We can''t compete with Mom! Her company is big and evil! Ours is small and neutral! Spare me your space age technobabble, Attila the Hun! What''s with you kids? Every other day it''s food, food, food. Alright, I''ll get you some stupid food.</p>
<p>Hey! I''m a porno-dealing monster, what do I care what you think? Shut up and take my money! THE BIG BRAIN AM WINNING AGAIN! I AM THE GREETEST! NOW I AM LEAVING EARTH, FOR NO RAISEN! Oh God, what have I done?</p>
<p>And yet you haven''t said what I told you to say! How can any of us trust you? No! I want to live! There are still too many things I don''t own! Whoa a real live robot; or is that some kind of cheesy New Year''s costume?</p>
<p>Kids have names? Leela, are you alright? You got wanged on the head. You, a bobsleder!? That I''d like to see! Bender, this is Fry''s decision… and he made it wrong. So it''s time for us to interfere in his life.</p>
<p>Hey, what kinda party is this? There''s no booze and only one hooker. I was having the most wonderful dream. Except you were there, and you were there, and you were there! So I really am important? How I feel when I''m drunk is correct?</p>
<p>Doomsday device? Ah, now the ball''s in Farnsworth''s court! I am Singing Wind, Chief of the Martians. Belligerent and numerous. Alright, let''s mafia things up a bit. Joey, burn down the ship. Clamps, burn down the crew.</p>
<p>Why would I want to know that? Who am I making this out to? Oh God, what have I done? Wow, you got that off the Internet? In my day, the Internet was only used to download pornography. Fetal stemcells, aren''t those controversial?</p>
<p>I suppose I could part with ''one'' and still be feared… Ugh, it''s filthy! Why not create a National Endowment for Strip Clubs while we''re at it? Robot 1-X, save my friends! And Zoidberg! I guess if you want children beaten, you have to do it yourself.</p>
<p>I never loved you. Enough about your promiscuous mother, Hermes! We have bigger problems. We''re rescuing ya. Bender, quit destroying the universe!</p>'),
(2, 1, 4, '2019-01-05 16:42:12', '2019-02-10 23:54:10', 0, 'Article modifié', 'Un autre article du site qui a subit des modifications.', '<h2>Un article.</h2><p>When will that be? Incidentally, you have a dime up your nose. I videotape every customer that comes in here, so that I may blackmail them later. No. We''re on the top. And remember, don''t do anything that affects anything, unless it turns out you were supposed to, in which case, for the love of God, don''t not do it!</p>
<p>Hey, guess what you''re accessories to. I am Singing Wind, Chief of the Martians. Wow! A superpowers drug you can just rub onto your skin? <strong> You''d think it would be something you''d have to freebase.</strong> <em> It may comfort you to know that Fry''s death took only fifteen seconds, yet the pain was so intense, that it felt to him like fifteen years.</em> And it goes without saying, it caused him to empty his bowels.</p>
<h2>Yeah. Give a little credit to our public schools.</h2>
<p>This is the worst part. The calm before the battle. Son, as your lawyer, I declare y''all are in a 12-piece bucket o'' trouble. But I done struck you a deal: Five hours of community service cleanin'' up that ol'' mess you caused.</p>
<ol>

    <li>If rubbin'' frozen dirt in your crotch is wrong, hey I don''t wanna be right.</li><li>Yep, I remember. They came in last at the Olympics, then retired to promote alcoholic beverages!</li><li>No, she''ll probably make me do it.</li>

</ol>

<h3>I don''t ''need'' to drink. I can quit anytime I want!</h3>
<p>Uh, is the puppy mechanical in any way? Then we''ll go with that data file! Bender, quit destroying the universe! It doesn''t look so shiny to me. Who are you, my warranty?! Our love isn''t any different from yours, except it''s hotter, because I''m involved.</p>
<ul>

    <li>They''re like sex, except I''m having them!</li><li>You mean while I''m sleeping in it?</li><li>No! I want to live! There are still too many things I don''t own!</li>

</ul>

<p>That could be ''my'' beautiful soul sitting naked on a couch. If I could just learn to play this stupid thing. Ummm…to eBay? I don''t ''need'' to drink. I can quit anytime I want! This is the worst kind of discrimination: the kind against me!</p>
<p>Oh right. I forgot about the battle. And why did ''I'' have to take a cab? Perhaps, but perhaps your civilization is merely the sewer of an even greater society above you! You''re going to do his laundry?</p>
<p>Guess again. No! The cat shelter''s on to me. We''ll need to have a look inside you with this camera. We need rest. The spirit is willing, but the flesh is spongy and bruised.</p>
<p>I suppose I could part with ''one'' and still be feared… Hey, what kinda party is this? There''s no booze and only one hooker. You know the worst thing about being a slave? They make you work, but they don''t pay you or let you go.</p>
<p>And then the battle''s not so bad? We need rest. The spirit is willing, but the flesh is spongy and bruised. This is the worst kind of discrimination: the kind against me! What are you hacking off? Is it my torso?! ''It is!'' My precious torso!</p>
<p>Too much work. Let''s burn it and say we dumped it in the sewer. I am Singing Wind, Chief of the Martians. Say it in Russian! Yes, I saw. You were doing well, until everyone died. I''m sure those windmills will keep them cool.</p>
<p>Anyhoo, your net-suits will allow you to experience Fry''s worm infested bowels as if you were actually wriggling through them. Fetal stemcells, aren''t those controversial? Dr. Zoidberg, that doesn''t make sense. But, okay!</p>
<p>Daylight and everything. Of all the friends I''ve had… you''re the first. That''s right, baby. I ain''t your loverboy Flexo, the guy you love so much. You even love anyone pretending to be him! Good news, everyone! I''ve taught the toaster to feel love!</p>
<p>Bender! Ship! Stop bickering or I''m going to come back there and change your opinions manually! Well, then good news! It''s a suppository. And why did ''I'' have to take a cab? Guards! Bring me the forms I need to fill out to have her taken away!</p>
<p>There''s no part of that sentence I didn''t like! Ummm…to eBay? I''m sorry, guys. I never meant to hurt you. Just to destroy everything you ever believed in. I had more, but you go ahead. I''m a thing.</p>
<p>Quite possible. We live long and are celebrated poopers. Yeah, I do that with my stupidness. Why not indeed! Professor, make a woman out of me. Why not indeed!</p>'),
(3, 1, null, '2019-02-18 14:20:12', '2019-02-18 14:20:12', 1, 'Article écrit en Markdown', 'Un article écrit en Markdown. Merci fillerama.io !', '## Cet article contient du **Markdown** et c''est bien *sympa*.
Good man. Nixon''s pro-war and pro-family. With a warning label this big, you know they gotta be fun! Good man. Nixon''s pro-war and pro-family. Well, let''s just dump it in the sewer and say we delivered it.

With gusto. Is that a cooking show? It''s just like the story of the grasshopper and the octopus. All year long, the grasshopper kept burying acorns for winter, while the octopus mooched off his girlfriend and watched TV. __But then the winter came, and the grasshopper died, and the octopus ate all his acorns.__ *Also he got a race car.* Is any of this getting through to you?

## Soothe us with sweet lies.

Yes, I saw. You were doing well, until everyone died. Hey, what kinda party is this? There''s no booze and only one hooker. THE BIG BRAIN AM WINNING AGAIN! I AM THE GREETEST! NOW I AM LEAVING EARTH, FOR NO RAISEN!

1. And I''m his friend Jesus.
2. I videotape every customer that comes in here, so that I may blackmail them later.
3. WINDMILLS DO NOT WORK THAT WAY! GOOD NIGHT!

### Leela, are you alright? You got wanged on the head.

We need rest. The spirit is willing, but the flesh is spongy and bruised. That''s the ONLY thing about being a slave. Oh right. I forgot about the battle. Fry, you can''t just sit here in the dark listening to classical music.

* Dear God, they''ll be killed on our doorstep! And there''s no trash pickup until January 3rd.
* Or a guy who burns down a bar for the insurance money!
* There''s no part of that sentence I didn''t like!

Why yes! Thanks for noticing. I barely knew Philip, but as a clergyman I have no problem telling his most intimate friends all about him. Throw her in the brig. If rubbin'' frozen dirt in your crotch is wrong, hey I don''t wanna be right.

No, just a regular mistake. Dr. Zoidberg, that doesn''t make sense. But, okay! We don''t have a brig. Yep, I remember. They came in last at the Olympics, then retired to promote alcoholic beverages! Okay, I like a challenge.

You can see how I lived before I met you. Who said that? SURE you can die! You want to die?! Oh right. I forgot about the battle. There''s one way and only one way to determine if an animal is intelligent. Dissect its brain!

You seem malnourished. Are you suffering from intestinal parasites? And remember, don''t do anything that affects anything, unless it turns out you were supposed to, in which case, for the love of God, don''t not do it!

All I want is to be a monkey of moderate intelligence who wears a suit… that''s why I''m transferring to business school! Throw her in the brig. I love you, buddy! You can see how I lived before I met you.

I feel like I was mauled by Jesus. Bender, you risked your life to save me! The key to victory is discipline, and that means a well made bed. You will practice until you can make your bed in your sleep.

For the last time, I don''t like lilacs! Your ''first'' wife was the one who liked lilacs! Daylight and everything. Say what? She also liked to shut up!

Switzerland is small and neutral! We are more like Germany, ambitious and misunderstood! I could if you hadn''t turned on the light and shut off my stereo. And when we woke up, we had these bodies. You''ve killed me! Oh, you''ve killed me!

One hundred dollars. What are you hacking off? Is it my torso?! ''It is!'' My precious torso! How much did you make me? Check it out, y''all. Everyone who was invited is here. Soothe us with sweet lies.

In your time, yes, but nowadays shut up! Besides, these are adult stemcells, harvested from perfectly healthy adults whom I killed for their stemcells. Bender?! You stole the atom. With gusto. You are the last hope of the universe.

We can''t compete with Mom! Her company is big and evil! Ours is small and neutral! I guess if you want children beaten, you have to do it yourself. Large bet on myself in round one. Okay, it''s 500 dollars, you have no choice of carrier, the battery can''t hold the charge and the reception isn''t very…'),
(4, 3, null, '2019-02-02 10:52:45', '2019-02-02 10:52:45', 0, 'En bref', 'Je suis Nicolas Renvoisé, 31 ans à l''heure où j''écris cet article, et je me forme au métier de développeur backend avec l''aide d''OpenClassrooms. Pourquoi ? Parce que j''ai découvert que le code, j''adore ça !!!', '<h2>Comment j''en suis arriv&eacute; l&agrave; ?</h2>
<p>Au d&eacute;part, j''ai fais 3 ans d''&eacute;tudes dans le domaine de l''eau et de l''environnement apr&egrave;s mon bac scientifique. A l''issue, je me suis engag&eacute; dans l''arm&eacute;e en tant que contr&ocirc;leur a&eacute;rien. Finalement, l''arm&eacute;e ce n''&eacute;tait pas aussi bien que ce que j''esp&eacute;rais et j''ai arr&ecirc;t&eacute; &agrave; la fin de mon contrat initial. J''ai alors int&eacute;gr&eacute; un bureau d''&eacute;tudes sp&eacute;cialis&eacute; dans l''eau et l''environnement o&ugrave; je me suis d&eacute;couvert une passion pour la programmation lorsque j''ai eu &agrave; faire des fichiers excel en VBA. Depuis ce jour, je n''arr&ecirc;te pas de coder !</p>
<p>Un ami programmeur m''a conseill&eacute; de suivre les cours du site du z&eacute;ro sur le langage C pour d&eacute;buter sur de bonnes bases. C''est alors que j''ai d&eacute;couvert que le site du z&eacute;ro &eacute;tait devenu OpenClassrooms et je m''y suis inscrit.</p>
<p>Apr&egrave;s le cours de Mathieu Nebra sur le C (un super cours, je le recommande !), j''ai enchain&eacute; sur les cours li&eacute;s au web, domaine qui m''intriguait beaucoup. Entre temps, j''en avais marre de mon travail au bureau d''&eacute;tudes (la bo&icirc;te &eacute;tait super, mais je n''aimais pas r&eacute;diger des rapports de 600 pages refoul&eacute;s pour un oui ou pour un non par l''administration...) et finalement j''ai d&eacute;cid&eacute; de partir pour me reconvertir dans le d&eacute;veloppement web en suivant la formation de d&eacute;veloppeur d''application PHP/Symfony d''OpenClassrooms.</p>
<h2>Pourquoi cette formation et pas une autre ?</h2>
<p>Une &eacute;cole Simplon s''&eacute;tait ouverte &agrave; 40 minutes de chez moi depuis 2 ans, mais rien n''indiquait que la formation gratuite qu''elle proposait aller &ecirc;tre reconduite. Du coup j''ai opt&eacute; pour OpenClassrooms en demandant un financement de la part de P&ocirc;le emploi, ce qui fut accept&eacute; ! Ouf ! Du coup non seulement j''ai l''avantage de suivre une formation reconnue par l''Etat, mais en plus je n''ai pas &agrave; me d&eacute;placer !</p>
<h2>Comment se passe cette formation ?</h2>
<p>Bien. Vraiment tr&egrave;s bien m&ecirc;me ! Je m''&eacute;clate tous les jours &agrave; programmer, d&egrave;s que je s&egrave;che je peux en parler soit avec mon mentor, soit avec les autres &eacute;tudiants d''OpenClassrooms. Je n''ai jamais aussi bien v&eacute;cu une formation.</p>'),
(5, 3, null, '2019-02-03 11:00:01', '2019-02-03 11:00:01', 1, 'Pourquoi ce site ?', 'Ce site présente mes réalisations web, mon CV et des articles sur divers thèmes. Je l''ai créé pendant ma formation de développeur backend (c''est le projet 5).', 'Voici quelques liens utiles pour naviguer sur le site :

* [Mon CV](/cv/cv.pdf)
* [Mes réalisations](/blog?category-id=3)
* [Le blog](/blog?category-id=1)'),
(6, 3, null, '2019-01-06 16:23:14', '2019-01-06 16:23:14', 0, 'Mes sites web perso', 'Les 2 sites que j''ai réalisés avant ma formation de développeur backend, juste à l''aide des cours gratuits d''OpenClassrooms. Le premier est un site sur le barefoot running et le second est un site pour s''orienter en balade.', '<h2>Les sites web</h2>

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
(7, 3, null, '2019-01-07 14:51:11', '2019-01-07 14:51:11', 0, 'Mes projets réalisés pendant la formation d''OpenClassrooms', 'Voici les liens vers les projets mis en ligne. Vous trouverez aussi les fichiers sources sur mon profil GitHub.', '<h2>Les projets en ligne</h2>

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
</p>'),
(8, 3, null, '2019-02-25 22:50:23', '2019-02-25 22:50:23', 0, 'Recueil CSS', 'Un recueil non exhaustif de propriétés CSS que je rencontre.', '            <article>
                <h2>Les propriétés</h2>

                <table>
                    <tr>
                        <th>Propriété</th>
                        <th>Définition</th>
                    </tr>
                    <tr>
                        <td><code>padding</code></td>
                        <td>
                            Gère l''espacement entre le texte et les limites du conteneur.<br />
                            <code>padding: Haut Droite Bas Gauche;</code> Pour renseigner en une fois les 4 directions.<br />
                            <code>padding: (Haut Bas) (Gauche Droite);</code> Si le haut = le bas et la gauche = la droite on peut ne mettre que 2 valeurs.
                        </td>
                    </tr>
                    <tr>
                        <td><code>margin</code></td>
                        <td>
                            Gère l''espacement entre les blocs.<br />
                            <code>margin: Haut Droite Bas Gauche;</code> Pour renseigner en une fois les 4 directions.<br />
                            <code>margin: (Haut Bas) (Gauche Droite);</code> Si le haut = le bas et la gauche = la droite on peut ne mettre que 2 valeurs.
                        </td>
                    </tr>
                    <tr>
                        <td><code>::before et ::after</code></td>
                        <td>
                            Permet d''afficher quelquechose avant (:before) ou après (:after) le bloc.<br />
                            Exemple : <em class="css-before-after">World</em><br />
                            Ici on insère <em>''Hello ''</em> avec <code>nom-classe:before { content: ''Hello ''; }</code> et <em>'' !''</em> avec <code>nom-classe:after { content: '' !''; }</code>
                        </td>
                    </tr>
                    <tr>
                        <td><code>overflow</code></td>
                        <td>
                            Choisi le comportement à adopter quand le texte dépasse d''un bloc.<br />
                            <ul>
                                <li><code>overflow: visible</code> Le texte reste visible à l''extérieur du bloc <em>(par défaut)</em></li>
                                <li><code>overflow: hidden</code> Masque le texte qui dépasse</li>
                                <li><code>overflow: scroll</code> Ajoute des barres de défilement au bloc</li>
                                <li><code>overflow: auto</code> Ajoute des barres de dévilement au bloc uniquement si besoin <em>(valeur conseillée)</em></li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td><code>div:nth-of-type(even)</code></td>
                        <td>
                            <code>div:nth-of-type(even)</code> Applique la mise en forme aux div pairs<br />
                            <code>div:nth-of-type(odd)</code> Applique la mise en forme aux div impaires<br />
                            <code>div:nth-of-type(an+b)</code> Forme générale où <em>a</em> et <em>b</em> sont des nombres entiers<br />
                            Utile pour mettre en valeur les lignes d''un tableau. Exemple sur un tableau WordPress :
<pre>
.wp-block-table tbody tr:nth-of-type(even) {
    background-color: rgba(0, 0, 0, 0.05);
}
</pre>
                        </td>
                    </tr>
                    <tr>
                        <td><code>white-space: nowrap;</code></td>
                        <td>
                            Empêche le retour à la ligne possible à chaque espace
                        </td>
                    </tr>
                    <tr>
                        <td><code>opacity: 0.42;</code></td>
                        <td>
                            Permet de régler la transparence d''un élément. De 0 = invisible à 1 = visible complètement.
                        </td>
                    </tr>
                </table>
            </article>

            <article>
                <h2>Les sélecteurs</h2>

                <dl>
                    <dt>
                        Les liens Link - Visited - Hover - Active (LoVe HAte)
                        <ul>
                            <li>
                                <code>a:visited</code>
                            </li>
                        </ul>
                    </dt>
                    <dd>
                        Pour les liens déjà visités.
                    </dd>
                </dl>
            </article>'),
(9, 3, null, '2019-02-12 12:52:44', '2019-02-12 12:52:44', 0, 'Recueil PHP', 'Des infos que je découvre en utilisant PHP.', '<article>
                <h2>Serveur</h2>

                <dl>
                    <dt>
                        <code>php -S localhost:8000</code>
                    </dt>
                    <dd>
                        Lance un serveur local qu''on peut accéder sur le navigateur en tapant <code>http://localhost:8080/</code> dans la barre d''adresse. Commande a exécuter dans le dossier contenant le fichier <code>index.php</code> ou exécuter <code>php -S localhost:8000 -t public</code> depuis le dossier du site dans le cas où <code>index.php</code> est dans le répertoire <code>public</code>.
                    </dd>
                </dl>
            </article>

            <!-- Les variables -->
            <article>
                <h2>Les variables</h2>

                <h3>Variables statiques</h3>

                <div>
                    <p>
                        Variables utilisées dans des fonctions dont la valeur sera gardée au prochain appel de la fonction. Utile pour faire de la récursivité.
                    </p>
                    <pre>
function testStaticVariables()
{
    static $a = 0;
    echo $a;
    $a++;
}

$i = 0;
while ($i < 5) {
	testStaticVariables();
	$i++;
} // Affiche 01234

function testStaticVariablesWithRecursion()
{
    static $count = 0;

    $count++;
    echo $count;
    if ($count < 10) {
        testStaticVariablesWithRecursion();
    }
    $count--;
}

testStaticVariablesWithRecursion(); // Affiche 0123456789
                    </pre>
                </div>
            </article>

            <!-- Les tableaux -->
            <article>
                <h2>Les tableaux</h2>

                <dl>
                    <dt>
                        Accéder au dernier élément d''un tableau avec <code>$myArray[-1]</code>
                    </dt>

                    <dt>
                        <code>$myArray[] = $myVar</code>
                    </dt>
                    <dd>
                        Ajoute un élément à la fin du tableau
                    </dd>

                    <dt>
                        <code>int array_push ( array &$array [, mixed $... ] )</code>
                    </dt>
                    <dd>
                        array_push() considère array comme une pile, et empile les variables var, ... à la fin de array. La longueur du tableau array augmente d''autant.
                    </dd>

                    <dt>
                        <code>array array_merge ( array $array1 [, array $... ] )</code>
                    </dt>
                    <dd>
                        array_merge() rassemble les éléments d''un ou de plusieurs tableaux en ajoutant les valeurs de l''un à la fin de l''autre. Le résultat est un tableau.

                        Si les tableaux d''entrées ont des clés en commun, alors, la valeur finale pour cette clé écrasera la précédente. Cependant, si les tableaux contiennent des clés numériques, la valeur finale n''écrasera pas la valeur originale, mais sera ajoutée.

                        Les clés numériques des tableaux d''entrées seront renumérotées en clés incrémentées partant de zéro dans le tableau fusionné.
                    </dd>

                    <dt>
                        Boucler sur un tableau avec <code>foreach ($posts as $post)</code> ou <code>foreach ($posts as $key => $value)</code> ou encore <code>foreach ($posts as [''aParticularKey'' => $value])</code> pour récupérer uniquement les valeurs d''une clé particulière
                    </dt>
                    <dd>
                        Note : on peut aussi utiliser une boucle <code>for ($i = 0, $size = count($myArray); $i < $count; $i++) { // Do things with $myArray[$i] }</code>
                    </dd>

                    <dt>
                        <code>array_reduce ( array $array , callable $callback [, mixed $initial = NULL ] ) : mixed</code>
                    </dt>
                    <dd>
                        Applique le callback à tous les éléments du tableau et retourne le résultat. Voici la forme du callback : <code>callback ( mixed $carry , mixed $item ) : mixed</code>. Si l''argument optionnel <code>$initial</code> est disponible, il sera utilisé pour initialiser le processus, ou bien comme valeur finale si le tableau est vide.
                    </dd>
                </dl>
            </article>

            <!-- Les closures -->
            <article>
                <h2>Les fonctions anonymes ou <em>closures</em></h2>

                <div>
                    <p>Exemple</p>
                    <pre>
$myVar = ''Hello world!'';

$myClosure = function () use ($myVar) {
    echo $myVar;
};

$myClosure();
                    </pre>

                    <p>Utilisation d''une closure comme paramètre d''une fonction (type <em>callable</em>)</p>
                    <pre>
/*
 * Exécute une fonction de callback
 */
function doACallback(callable $callback) // callable : pour avoir quelque chose d''appelable, comme une closure ou un objet ayant une méthode <code>__invoke()</code>.
{
    $callback();
}

doACallback(function () {
    echo ''zog'';
});

$myVar = ''zogzog'';

doACallback(function() use ($myVar) {
    echo $myVar;
});
                    </pre>

                    <p>Cas de closures dans une classe</p>
                    <pre>
// Cas où l''on utilise $this dans une closure d''une méthode : $this sera automatiquement lié à l''objet
class Test
{
    public function testing()
    {
        return function() {
            var_dump($this);
        };
    }
}

$object = new Test;
$function = $object->testing();
$function(); // Output : object(Test)#1 (0) {}

// Pour éviter ce comportement, on peut déclarer une closure statique
class Foo
{
    function __construct()
    {
        $func = static function() {
            var_dump($this);
        };
        $func();
    }
};
new Foo(); // Affiche Notice: Undefined variable: this in %s on line %d NULL
                    </pre>
                </div>
            </article>

            <!-- Exceptions -->
            <article>
                <h2>Les exceptions</h2>

                <p>
                    Liste des exceptions pré-définies <a href="http://fr2.php.net/manual/fr/spl.exceptions.php">ici.</a>
                </p>
            </article>

            <!-- Fonctions pratiques -->
            <article>
                <h2>Fonctions pratiques</h2>

                <h3>Variables</h3>

                <dl>
                    <dt>
                        <code>bool isset ( mixed $var [, mixed $... ] )</code>
                    </dt>
                    <dd>
                        Détermine si une variable est définie et est différente de NULL. <br>
                        Si plusieurs paramètres sont fournis, alors la fonction isset() retournera TRUE seulement si tous les paramètres sont définis. L''évaluation s''effectue de gauche vers la droite et s''arrête dès qu''une variable non définie est rencontrée.
                    </dd>

                    <dt>
                        <code>void unset ( mixed $var [, mixed $... ] )</code>
                    </dt>
                    <dd>
                        unset() détruit la ou les variables dont le nom a été passé en argument var. Peut servir à supprimer un élément d''un tableau.
                    </dd>

                    <dt>
                        <code>mixed filter_var ( mixed $variable [, int $filter = FILTER_DEFAULT [, mixed $options ]] )</code>
                    </dt>
                    <dd>
                        Permet de filtrer des données. Retourne les données filtrées ou false si le filtre a échoué. Utile pour vérifier les adresses email par exemple avec <code>if (filter_var($email, FILTER_VALIDATE_EMAIL) {}</code>
                    </dd>

                    <dt>
                        <code>extract ( array &$array [, int $flags = EXTR_OVERWRITE [, string $prefix = NULL ]] ) : int</code>
                    </dt>
                    <dd>
                        Créé des variables à partir des éléments d''un tableau. Retourne le nombre de variables importées avec succès dans la table des symboles (symbole = variable).
                    </dd>
                </dl>

                <h3>Fonctions</h3>

                <dl>
                    <dt>
                        <code>array func_get_args ( void )</code>
                    </dt>
                    <dd>
                        Récupère les arguments d''une fonction sous la forme d''un tableau.
                    </dd>

                    <dt>
                        <code>mixed func_get_arg ( int $arg_num )</code>
                    </dt>
                    <dd>
                        Récupère un élément de la liste des arguments d''une fonction utilisateur. Les arguments sont comptés à partir de 0.
                    </dd>

                    <dt>
                        <code>int func_num_args ( void )</code>
                    </dt>
                    <dd>
                        Récupère le nombre d''arguments passés à la fonction.
                    </dd>
                </dl>

                <h3>Fichiers</h3>

                <dl>
                    <dt>
                        <code>string file_get_contents ( string $filename [, bool $use_include_path = FALSE [, resource $context [, int $offset = 0 [, int $maxlen ]]]] )</code>
                    </dt>
                    <dd>
                        Lit tout un fichier dans une chaîne.
                    </dd>

                    <dt>
                        <code> unlink ( string $filename [, resource $context ] ) : bool</code>
                    </dt>
                    <dd>
                        Efface un fichier.
                    </dd>
                </dl>

                <h3>POO</h3>

                <dl>
                    <dt>
                        <code>get_called_class()</code>
                    </dt>
                    <dd>
                        Retourne le nom de la classe depuis laquelle une méthode statique a été appelée, tel que le Late State Binding le détermine.
                    </dd>

                    <dt>
                        <code>get_class([ object $object ])</code>
                    </dt>
                    <dd>
                        Retourne le nom de la classe de l''objet. Le paramètre <code>$object</code> peut être omis lorsque la fonction est utilisée dans une classe.
                    </dd>

                    <dt>
                        <code>is_object($myArgument)</code>
                    </dt>
                    <dd>
                        Retourne true si l''argument est un objet.
                    </dd>
                </dl>

                <h3>Chaînes</h3>

                <dl>
                    <dt>
                        <code>$myString[-1]</code>
                    </dt>
                    <dd>
                        Permet d''accéder au dernier caractère de la chaîne.
                    </dd>

                    <dt>
                        <code>explode(''séparateur'', ''Chaîne à exploser'')</code>
                    </dt>
                    <dd>
                        Retourne un tableau contenant les morceaux de la chaîne à explosés séparées par le séparateur.
                    </dd>

                    <dt>
                        <code>strtolower(''Chaîne a mettre en minuscule'')</code> ou mieux : <code> mb_strtolower ( string $str [, string $encoding = mb_internal_encoding() ] ) : string</code>
                    </dt>
                    <dd>
                        Retourne la chaîne en minuscule. Préferer <code>mb_strtolower($string, ''UTF-8'')</code> qui fonctionne aussi pour les accents.
                    </dd>

                    <dt>
                        <code>string substr ( string $string , int $start [, int $length ] )</code>
                    </dt>
                    <dd>
                        Retourne le segment de <code>$string</code> commençant par <code>$start</code> et de <code>$length</code> caractères de long. Si on met une <code>$length</code> négative, alors ça enlève les caractères en partant de la fin de la chaîne.
                    </dd>
                </dl>

                <h3>Tableaux</h3>

                <dl>
                    <dt>
                        <code>end(array)</code>
                    </dt>
                    <dd>
                        Retourne le dernier élément du tableau en paramètre ou false si le tableau est vide.
                    </dd>

                    <dt>
                        <code>array compact($myVar1, $myVar2, ...)</code>
                    </dt>
                    <dd>
                        Crée un tableau à partir de variables et de leur valeur. So compact(''var1'', ''var2'') is the same as saying array(''var1'' => $var1, ''var2'' => $var2) as long as $var1 and $var2 are set.
                    </dd>

                    <dt>
                        <code>int extract(array &$array [, int $flags = EXTR_OVERWRITE [, string $prefix = NULL ]])</code>
                    </dt>
                    <dd>
                        Fait l''inverse de <code>compact</code>.
                    </dd>
                </dl>

                <h3>Astuces</h3>

                <dl>
                    <dt>
                        <code>uniqid([ string $prefix = "" [, bool $more_entropy = FALSE ])</code>
                    </dt>
                    <dd>
                        Génère un identifiant unique, préfixé, basé sur la date et heure courante en microsecondes. <br>
                        Si <code>$more_entropy</code> est <code>true</code> alors on augmente la probabilité de l''unicité du résultat.
                    </dd>
                </dl>
            </article>

            <article>
                <h2>Debugging</h2>

                <table>
                    <tr>
                        <th>Instruction</th>
                        <th>Description</th>
                    </tr>
                    <tr>
                        <td>
                            <code>void exit ([ string $status ] )</code>
                        </td>
                        <dd>
                            Arrête le script. On peut passer une variable ou une chaîne à <code>exit()</code> pour l''afficher avant d''arrêter le bazar.
                        </dd>

                        <td>
                            <code>void die ([ string $status ] )</code>
                        </td>
                        <td>
                            Arrête le script. On peut passer une variable ou une chaîne à <code>die()</code> pour l''afficher avant d''arrêter le bazar.
                        </td>

                    </tr>
                    <tr>
                        <td>
                            <code>var_dump($variable1, $variable2, ...)</code>
                        </td>
                        <td>
                            Affiche le contenu d''une ou plusieurs variables.
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <code> print_r ( mixed $expression [, bool $return = FALSE ] ) : mixed)</code>
                        </td>
                        <td>
                            Affiche le contenu d''un tableau. En mettant <code>$return</code> à <code>true</code> la fonction retournera le résultat au lieu de l''afficher.
                        </td>
                    </tr>
                </table>
            </article>

            <article>
                <h2>Liens utiles</h2>

                <ul>
                    <li>
                        Bonnes pratiques : <a href="https://www.php-fig.org/psr/">Liste des PSR (PHP Standard Recommendations)</a>
                        <ul>
                            <li>
                                <a href="https://www.php-fig.org/psr/psr-1/">PSR-1: Basic Coding Standard</a>
                            </li>
                            <li>
                                <a href="https://www.php-fig.org/psr/psr-2/">PSR-2: Coding Style Guide</a>
                            </li>
                            <li>
                                <a href="https://www.php-fig.org/psr/psr-4/">PSR-4: Autoloader</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <dl>
                            <dt>
                                <a href="https://www.jetbrains.com/phpstorm/">PhpStorm</a>
                            </dt>
                            <dd>
                                Un IDE pour PHP.
                            </dd>
                        </dl>
                    </li>
                    <li>
                        <dl>
                            <dt>
                                <a href="https://getcomposer.org/">Composer</a>
                            </dt>
                            <dd>
                                Permet d''installer des librairies facilement.<br />
                                <a href="https://www.grafikart.fr/tutoriels/composer-480">Tuto de Grafikart</a>
                            </dd>
                        </dl>
                    </li>
                    <li>
                        <dl>
                            <dt>
                                <a href="https://twig.symfony.com/">Twig</a>
                            </dt>
                            <dd>
                                Moteur de templates, utilisé entre autres par le framework Symfony.<br />
                                <a href="https://www.grafikart.fr/tutoriels/twig-832">Tuto de Grafikart</a>
                            </dd>
                        </dl>
                    </li>
                </ul>
            </article>

            <article>
                <h2>Design pattern</h2>

                <h3>Singleton</h3>

                <div>
                    <pre>
namespace Core;

class Config
{

    private $settings = [];
    private static $_instance; // L''attribut qui stockera l''instance unique

    /**
    * La méthode statique qui permet d''instancier ou de récupérer l''instance unique
    **/
    public static function getInstance($file)
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Config($file);
        }
        return self::$_instance;
    }

    /**
    * Le constrcuteur avec sa logique est privé pour émpêcher l''instanciation en dehors de la classe
    **/
    private function __construct($file)
    {
        $this->settings = require($file);
    }

    /**
    *  Permet d''obtenir la valeur de la configuration
    *  @param $key string clef à récupérer
    *  @return mixed
    **/
    public function get($key)
    {
        if (!isset($this->settings[$key])) {
            return null;
        }
        return $this->settings[$key];
    }

}
                    </pre>
                </div>

                <h3>Factory</h3>

                <div>
                    <p>Principe</p>
                    <pre>
class Voiture
{
    private $marque;

    public function __construct($marque)
    {
        $this->marque = $marque;
    }

    public function getName()
    {
        return ''Voiture '' . $this->marque;
    }
}

class VoitureFactory
{
    public static function create($marque)
    {
        return new Voiture($marque);
    }
}

$twingo = VoitureFactory::create(''Twingo'');
$c3 = VoitureFactory::create(''C3'');
                    </pre>

                    <p>Version ++</p>
                    <pre>
class ArticleTable(){  }

class VoitureTable(){  }

class UtilisateurTable(){  }

class TableFactory(){

    public static function create($table){
        $class_name = ucfirst($table) . ''Table'';
        return new $class_name();
    }

}

TableFactory::create(''Article'');
                    </pre>
                </div>

                <h3>Fluent</h3>

                <div>
                    <pre>
class QueryBuilder{

    private $fields = [];
    private $conditions = [];
    private $from = [];

    public function select(){
        $this->fields = func_get_args();
        return $this;
    }

    public function where(){
        foreach(func_get_args() as $arg){
            $this->conditions[] = $arg;
        }
        return $this;
    }

    public function from($table, $alias = null){
        if(is_null($alias)){
            $this->from[] = $table;
        }else{
            $this->from[] = "$table AS $alias";
        }
        return $this;
    }

    public function __toString(){
        return ''SELECT ''. implode('', '', $this->fields)
            . '' FROM '' . implode('', '', $this->from)
            . '' WHERE '' . implode('' AND '', $this->conditions);
    }

}

// Instructions
$query = new QueryBuilder();
$requete = $query->select(''id'', ''titre'', ''contenu'')->from(''articles'', ''Post'')->where(''Post.category_id = 1'')->where(''Post.date > NOW()'');
                    </pre>
                </div>

                <h3>Façade</h3>

                <div>
                    <pre>
class QueryFacade{

    public static function __callStatic($name, $arguments){
        $query = new \Core\Database\QueryBuilder();
        return call_user_func_array([$query, $name], $arguments);
    }

}

// On pourra ensuite l''utiliser en faisant
QueryFacade::select(''id'', ''titre'')->from(''articles'');

// Ce qui équivaut à faire :
$query = new \Core\Database\QueryBuilder();
$query->select(''id'', ''titre'')->from(''articles'');
                    </pre>
                </div>

                <h3>Injection de dépendances</h3>

                <div>
                    <pre>
class Article{

    private $database;

// On remplace  ça :
                        /*
    public function __construct(){
        $this->database = new MySQLDatabase(''blog'');
    }
                        */
// Par ça :
    public function __construct($database){
        $this->database = $database;
    }
}

// Et quand on construit l''objet
$db = new MySQLDatabase(''blog'');
$article = new Article($db);

// Maintenant on utilise un conteneur d''injection de dépendances :
class DIContainer{

    private static $db_instance;

    /**
    *  Permet de retourner un nouvel article
    *  @return Article
    **/
    public function getArticle()
    {
        return new Article($this->getDatabase());
    }

    /**
    * Permet de retourner une instance unique de la connexion à la base de donnée
    * @return MySQLDatabase
    **/
    public function getDatabase()
    {
        if(self::$db_instance){
            return new MySQLDatabase(''blog'');
        } else {
            return self::$db_instance;
        }
    }
}

$container = new DIContainer(); // On pourra créer un singleton par la suite si besoin
$article = $container->getArticle();
                    </pre>
                </div>

                <h3>Les interfaces</h3>

                <div>
                    <p>
                        Une interface oblige les classes qui l''implémente à définir les méthodes de l''interface.
                    </p>

                    <h4>Fichier MonInterface.php</h4>
                    <pre>
interface MonInterface
{
	const UNE_CONSTANTE = ''zog'';

	public function uneMethode($params);
}
                    </pre>

                    <h4>Fichier MaClasse.php</h4>
                    <pre>
class MaClasse implements MonInterface
{
	public function uneMethode($params)
	{
		echo self::UNE_CONSTANTE . $params;
	}
}
                    </pre>
                </div>

                <h3>Les traits</h3>

                <p>
                    Le principe des traits est de permettre de contourner les limites imposées par l''héritage simple de PHP. Le but est de permettre de créer de nouvelles méthodes et de nouvelles propriétés que l''on pourra ajouter à nos différentes classes de manière horizontale.
                </p>

                <div>
                    <h4>Exemple de trait</h4>
                    <pre>
trait FileHandler
{
    public $files = [];

    /**
     * @param $fullPath
     * @param string $mode
     */
    public function addAFile($fullPath, $mode = ''r'')
    {
        $fileName = self::getFileNameFromFullPath($fullPath);
        $this->files[] = [$fileName => self::openFile($fullPath, $mode)];
    }

    /**
     * @param $fullPath
     * @return string
     */
    public static function getFileNameFromFullPath($fullPath)
    {
        $bitsOfPath = explode(''/'', $fullPath);
        return end($bitsOfPath);
    }

    /**
     * @param $fileName string chemin du fichier
     * @param string $mode = ''r'' pour read only
     * @return bool|resource
     */
    public static function openFile($fileName, $mode = ''r'')
    {
        return fopen($fileName, $mode);
    }

    /**
     * @param $fileName
     * @return int
     */
    public static function countFileLines($fileName)
    {
        return substr_count(self::getFileContent($fileName), "\n");
    }

    /**
     * @param $fileName
     * @return false|string
     */
    public static function getFileContent($fileName)
    {
        return file_get_contents($fileName);
    }
}
                    </pre>

                    <h4>Utilisation de traits</h4>
                    <pre>
// Dans une classe
class TestClass
{
    use RandomStuff, WrapTag;
}

// Dans un trait
trait MyTrait3
{
	use MyTrait2
	{
		sayHello as protected;
		askHowAreYou as commentCaVa;
		sayBye as protected direAuRevoir;
	}
}

// Plusieurs traits
class MyClass2
{
	use MyTrait1, MyTrait2, MyTrait3 // Collision à cause des méthodes sayHello() dupliquées
	{
		MyTrait2::sayHello insteadof MyTrait1, MyTrait3; // On évite la collision en mettant la méthode conflictuelle d''un trait en priorité sur les autres.
		MyTrait2::askHowAreYou insteadof MyTrait1, MyTrait3;
		MyTrait2::sayBye insteadof MyTrait1, MyTrait3;
	}
}

class MyClass3Daugther extends MyClass3
{
	use MyTrait1 {
		sayBye as trait1SayBye;
	}

	public function sayBye() // Ici je n''ai pas mis de paramètre contrairement à la fonction sayBye de MyTrait1, du coup ça affiche un warning
	{
		$this->trait1SayBye(''zog'');
		echo ''Ici sayBye() de MyClass3Daugther'';
	}
}
                    </pre>
                </div>

                <h3>Conteneur d''injection de dépendances (DIC)</h3>

                <div>
                    <p>
                        DIC tout fait : <a href="https://pimple.symfony.com/">pimple</a>
                    </p>
                    <p>
                        Exemple de conteneur simple
                    </p>
                    <pre>
class DIC
{
    private $registeredSingleClosures = [];
    private $registeredClosures = [];
    private $instances = [];

    /**
     * @param $key
     * @param callable $closure
     */
    public function set($key, Callable $closure)
    {
        $this->registeredClosures[$key] = $closure;
    }

    /**
     * @param $key
     * @param callable $closure
     */
    public function setOnce($key, Callable $closure)
    {
        $this->registeredSingleClosures[$key] = $closure;
    }

    /**
     * @param $instance
     */
    public function setInstance($instance)
    {
        try {
            $reflection = new ReflectionClass($instance);
        } catch (ReflectionException $e) {
            echo ''Ya un soucis dans DIC::setInstance() avec ReflectionClass.'';
        }

        $this->instances[$reflection->getName()] = $instance;
    }

    /**
     * @param $key
     * @return mixed
     * @throws Exception
     */
    public function get($key)
    {
        // On regarde si la clé correspond à une closure qu''on peut exécuter plusieurs fois
        if (isset($this->registeredClosures[$key])) {
            return $this->registeredClosures[$key]();
        }

        // On regarde si on a déjà une instance de créée avec la clé
        if (!isset($this->instances[$key])) {
            if (isset($this->registeredSingleClosures[$key])) {
                $this->instances[$key] = $this->registeredSingleClosures[$key]();
            } else {
                throw new Exception($key . " is not a registered closure");
            }
        }
    }
}
                    </pre>
                    <p>
                        Utilisation du DIC
                    </p>
                    <pre>
// On enregistre les dépendances dans le conteneur
$dic = new DIC();
$dic->setOnce(''Connection'', function () {
    return new Connection(''test_grafikart_dic'', ''root'', '''');
});

$dic->set(''Model'', function() use ($dic) {
    return new Model($dic->get(''Connection''));
});

// On utilise le conteneur pour générer nos instances
$connection = $dic->get(''Connection'');
$model = $dic->get(''Model'');
                    </pre>
                </div>

                <h3>Adapter</h3>

                <div>
                    <p>
                        Permet de faire le lien entre des classes qui demandent des interfaces différentes.
                    </p>
                    <pre>
// On a une classe qui demande un objet implémentant l''interface CacheInterface
class Hello
{

    public function sayHello(CacheInterface $cache)
    {
        if($cache->has(''hello'')) {
            return $cache->get(''hello'');
        } else {
            sleep(4); // On simule un script lent
            $content = ''bonjour'';
            $cache->set(''hello'', $content);
            return $content;
        }
    }

}

// Voici l''interface CacheInterface
interface CacheInterface
{
    public function get($key);

    public function has($key);

    public function set($key, $value, $expiration = 3600);
}

// On veut utiliser le système de cache de Doctrine, du coup on utilise un adapter qui permettra de traduire les méthodes de Doctrine en méthodes de l''interface CacheInterface
class DoctrineCacheAdapter implements CacheInterface
{

    private $cache; // Notre objet venant d''une librairie tiers

    // On injecte notre objet dans le constructeur
    public function __construct(Doctrine\Common\Cache\Cache $cache)
    {
        $this->cache = $cache;
    }

    // On map toutes les méthodes de l''interface aux méthodes de l''objet
    public function get($key)
    {
        return $this->cache->fetch($key);
    }

    public function has($key)
    {
        return $this->cache->contains($key);
    }

    public function set($key, $value, $expiration = 3600)
    {
        return $this->cache->save($key, $value, $expiration);
    }
}

// On peut maintenant utiliser Doctrine dans notre application via l''adapter
$cache = new \Doctrine\Common\Cache\FilesystemCache(__DIR__ . ''/cache'');
// On "adapte" notre objet
$adapter = new DoctrineCacheAdapter($cache);
$hello = new Hello();
echo $hello->sayHello($adapter);
                    </pre>
                </div>

                <h3>Decorator</h3>

                <div>
                    <p>
                        Modifie le fonctionnement d''un objet sans modifier sa classe.
                    </p>
                    <p>
                        Utile car on ne peut pas faire de metaprogramming en php. On peut soit utiliser l''héritage ou une interface. L''interface est préférable car elle évite d''avoir à modifier les constructeurs.
                    </p>
                    <pre>
// L''interface utile pour les décorateurs
interface HelloInterface
{
    public function sayHello();
}

// Notre classe de base
class Hello implements HelloInterface
{
    public function sayHello()
    {
        return ''Bonjour'';
    }
}

// Décorateurs
class CaVaDecorator implements HelloInterface
{
    private $hello;

    public function __construct(HelloInterface $hello)
    {

        $this->hello = $hello;
    }

    public function sayHello()
    {
        return $this->hello->sayHello() . ''. Comment ça va ?'';
    }
}

class MerciDecorator implements HelloInterface
{
    private $hello;

    public function __construct(HelloInterface $hello)
    {

        $this->hello = $hello;
    }

    public function sayHello()
    {
        return $this->hello->sayHello() . '' Merci.'';
    }
}

// On créé un objet puis on le modifie avec des décorators
$hello = new Hello(); // ''Bonjour''
$hello = new CaVaDecorator($hello); // ''Bonjour. Comment ça va ?''
$hello = new MerciDecorator($hello); // ''Bonjour. Comment ça va ? Merci''

echo $hello->sayHello();
                    </pre>
                </div>
            </article>'),
(10, 3, null, '2019-02-18 11:42:31', '2019-02-18 11:42:31', 0, 'Recueil Git et GitHub', 'Des infos que je note en utilisant Git et GitHub.', '<!-- Git -->

    	<article>
            <h2>Git</h2>

    		<h3>Liste des commandes utiles de la console</h3>

            <!-- Commandes de la console -->
    		<table class="manualTable" id="handyCommandsTable">
    			<tr>
    				<th>Commande</th>
    				<th>Description</th>
                    <th class="tag">Tags</th>
    			</tr>
    			<tr>
    				<td><code>pwd</code></td>
    				<td>Donne le répertoire courant</td>
    			</tr>
    			<tr>
    				<td><code>ls</code></td>
    				<td>Donne la liste des fichiers et répertoires dans le dossier courant</td>
    			</tr>
    			<tr>
    				<td><code>ls -l</code></td>
    				<td>Affiche une liste des fichiers et répertoires du dossier courant</td>
    			</tr>
    			<tr>
    				<td><code>ls -a</code></td>
    				<td>Donne des infos supplémentaires</td>
    			</tr>
    			<tr>
    				<td><code>cd nomRépertoire</code></td>
    				<td>Permet de se placer dans un répertoire</td>
    			</tr>
    			<tr>
    				<td><code>cd ..</code></td>
    				<td>Permet d''aller au répertoire parent</td>
    			</tr>
    			<tr>
    				<td><code>cd ~</code></td>
    				<td>Revient au répertoire principal</td>
    			</tr>
    			<tr>
    				<td><code>touch nomFichier</code></td>
    				<td>Permet de créer un fichier</td>
    			</tr>
    			<tr>
    				<td><code>mkdir nomRépertoire</code></td>
    				<td>Permet de créer un dossier</td>
    			</tr>
    			<tr>
    				<td><code>car nomFichier</code></td>
    				<td>Affiche le contenu d''un fichier</td>
    			</tr>
                <tr>
                    <td><code>clear</code></td>
                    <td>Efface la console</td>
                </tr>
    			<tr>
    				<td><code>ctl + shift + inser</code></td>
    				<td>Permet de coller du texte dans la console</td>
    			</tr>
    		</table>

    		<h3>Utilisation de git</h3>

            <!-- Utilisation de git -->
    		<table class="manualTable" id="gitUseTable">
    			<tr>
    				<th>Commande</th>
    				<th>Description</th>
    			</tr>
    			<tr>
    				<td><code>git config --global user.name "nomDeLUtilisateur"</code></td>
    				<td>Configure le nom d''utilisateur de git</td>
    			</tr>
    			<tr>
    				<td><code>git config --global user.email "emailDeLUtilisateur"</code></td>
    				<td>Configure l''email de l''utilisateur de git</td>
    			</tr>
    			<tr>
    				<td><code>git init</code></td>
    				<td>Active le répertoire courant en repository git (ajoute un dossier caché .git au répertoire)</td>
    			</tr>
    			<tr>
    				<td><code>git status</code></td>
    				<td>Donne le statut du repository (fichiers indexés ou non pouvant faire l''objet d''un commit)</td>
    			</tr>
    			<tr>
    				<td><code>git log</code></td>
    				<td>Donne les différents commit du repository</td>
    			</tr>
    			<tr>
    				<td>Touche "q" du clavier</td>
    				<td>Permet de sortir du log</td>
    			</tr>
    			<tr>
    				<td><code>git add nomDuFichierAIndexer.extension</code></td>
    				<td>Ajoute un fichier à l''index de git pour pouvoir en faire un commit</td>
    			</tr>
    			<tr>
    				<td><code>git add .</code></td>
    				<td>Indexe tous les fichiers du repository en vue d''un commit</td>
    			</tr>
    			<tr>
    				<td><code>git commit -m "Entrez ici la description du commit"</code></td>
    				<td>Permet de créer un commit</td>
    			</tr>
    			<tr>
    				<td><code>git commit -a -m "Entrez ici la description du commit"</code></td>
    				<td>Le -a permet de créer un commit avec les fichiers qui ont déjà été indexés une fois et sans avoir à les réindexer avec add</td>
    			</tr>
    			<tr>
    				<td><code>git checkout SHADuCommit</code></td>
    				<td>Revenir à un commit précédent</td>
    			</tr>
    			<tr>
    				<td><code>git checkout master</code></td>
    				<td>Revenir au dernier commit</td>
    			</tr>
    			<tr>
    				<td><code>git revert SHADuCommit</code></td>
    				<td>Créé un nouveau commit qui fait exactement l''inverse du précédent (pour annuler le commit précédent)</td>
    			</tr>
    			<tr>
    				<td><code>git commit --amend -m "Votre nouveau message pour le dernier commit"</code></td>
    				<td>Modifie le message du dernier commit</td>
    			</tr>
    			<tr>
    				<td><code>git reset --hard</code></td>
    				<td>Annule les changements qui n''ont pas encore été commités</td>
    			</tr>
                <tr>
                    <td><code>git branch</code></td>
                    <td>Affiche les différentes branches du repository</td>
                </tr>
                <tr>
                    <td><code>git branch nomDeLaNouvelleBranche</code></td>
                    <td>Créé une nouvelle branche</td>
                </tr>
                <tr>
                    <td><code>git branch -d nomDeLaBrancheASupprimer</code></td>
                    <td>Supprime une branche</td>
                </tr>
                <tr>
                    <td><code>git checkout nomDeLaBranche</code></td>
                    <td>Se placer sur une branche</td>
                </tr>
                <tr>
                    <td><code>git checkout -b nomDeLaNouvelleBranche</code></td>
                    <td>Créé une nouvelle branche et nous place dessus</td>
                </tr>
                <tr>
                    <td><code>git merge nomDeLaBrancheAFusionner</code></td>
                    <td>
                        Permet de fusionner 2 branches<br />
                        Par exemple pour ajouter dans une branche A les mises à jour que vous avez faites dans une autre branche B, on se place dans la branche A avant d''exécuter la commande <code>git merge brancheB</code>
                    </td>
                </tr>
                <tr>
                    <td><code>git blame nomDuFichier.sonExtension</code></td>
                    <td>
                        Liste toutes les modifications qui ont été faites sur le fichier ligne par ligne. À chaque modification est associé le début du sha du commit correspondant.
                    </td>
                </tr>
                <tr>
                    <td><code>git show debutCommitSHA</code></td>
                    <td>
                        Affiche les détails du commit recherché en saisissant le début de son sha.
                    </td>
                </tr>
                <tr>
                    <td>Fichier <em>.gitignore</em></td>
                    <td>
                        Fichier à créer à la racine du projet. Il doit contenir les noms des fichiers à ignorer (par exemple des fichiers contenant des mots de passe).<br />
                        Exemple de contenu du fichier <em>.gitignore</em> :
                        <pre>
keys/monFichierDeClesAPI.zog
monFichierDeConfiguration.conf
nom_du_dossier_a_ignorer/
                        </pre>
                    </td>
                </tr>
                <tr>
                    <td><code>git stash</code> et <code>git stash pop</code> ou <code>git stash apply</code></td>
                    <td>
                        <p>
                            <code>git stash</code> met de côté les modifications en cours qui n''ont pas fait l''objet d''un commit pour pouvoir faire d''autres modifications, par exemple lorsque quelqu''un nous demande de régler un bug qui n''a rien à voir avec ce sur quoi on travaille actuellement. On stash notre travail, on règle le bug, on commit les modifications apportées pour régler le bug et on revient sur notre travail avec <code>git stash pop</code> ou <code>git stash apply</code>.
                        </p>
                        <p>
                            La différence entre <code>pop</code> et <code>apply</code> est que nos modifications sont effacées du stash lors d''un <code>pop</code>.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <code>git commit --amend</code>
                    </td>
                    <td>
                        <p>
                            Permet d''ajouter des modifications au dernier commit et de modifier le message associé.
                        </p>
                        <p>
                            Penser à faire un <code>git push origin master --force</code> à l''issue pour mettre à jour le commit de GitHUB.
                        </p>
                    </td>
                    <td class="tag">
                        Modifier le message d''un commit
                    </td>
                </tr>
                <tr>
                    <td>
                        <code>git rebase -i HEAD~nombreDeCommitsRealisesEnPartantDuPlusRecent</code> ou <code>git rebase -i --root</code>
                    </td>
                    <td>
                        <p>
                            Ouvre l''éditeur de texte et affiche de haut en bas le commit sélectionné et tous les commits réalisés après ou tous les commits depuis le début si on utilise <code>git rebase -i --root</code>.
                        </p>
                        <p>
                            Chaque commit est précédé de <code>pick</code>. On peut alors modifier le message d''un commit en remplaçant <code>pick</code> par <code>edit</code>. Une fois le fichier enregistré et l''éditeur quitté, on peut exécuter la commande <code>git commit --amend</code> pour procédé à la modification du message du commit. Dernière étape, exécuter <code>git rebase --continue</code>.
                        </p>
                        <p>
                            Penser à faire un <code>git push origin master --force</code> à l''issue pour mettre à jour le commit de GitHUB.
                        </p>
                    </td>
                    <td class="tag">
                        Modifier le message d''un commit
                    </td>
                </tr>
                <tr>
                    <td>
                        <code>git branch nomNouvelleBranche</code><br> On créé une nouvelle branche pour sauvegarder les derniers commits réalisés
                        <code>git reset --hard HEAD~3</code> pour revenir 3 commits en arrière ou <code>git reset --hard shaDuCommit</code> pour revenir à un commit particulier<br>
                        <code>git checkout nomNouvelleBranche</code> On revient sur la nouvelle branche
                    </td>
                    <td>
                        Déplacer les derniers commits de la branche master dans une nouvelle branche
                    </td>
                </tr>
    		</table>

            <h3>Edition du fichier de configuration</h3>

            <!-- Configuration de git -->
            <table class="manualTable" id="gitConfig">
                <tr>
                    <th>Action</th>
                    <th>Comment faire</th>
                </tr>
                <tr>
                    <td>Ouvrir le fichier de configuration de Git</td>
                    <td>
                        <ul>
                            <li>Se placer dans le répertoire personnel <code>C:\Users\nomDeLUtilisateur</code></li>
                            <li>Ouvrir le fichier <code>.gitconfig</code></li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td>Créer des alias pour aller plus vite</td>
                    <td>
                        <ul>
                            <li>Ouvrir le fichier de configuration de Git</li>
                            <li>
                                Ajouter à la fin les alias<br />
                                <pre>
[alias]
    ci = commit
    co = checkout
    st = status
    br = branch
                                </pre>
                            </li>
                            <li>Maintenant on peut taper juste <code>git st</code> pour afficher le status</li>
                        </ul>
                    </td>
                </tr>
            </table>
    	</article>

        <!-- GitHub -->

    	<article>
			<h2>GitHub</h2>

			<h3>Actions possibles</h3>

			<table>
				<tr>
					<th>Action</th>
					<th>Comment faire</th>
				</tr>
				<tr>
					<td>Récupérer un repository</td>
					<td>
						<ul>
							<li>Taper le nom du repository désiré dans la barre de recherche</li>
							<li>Cliquer sur Clone or download</li>
							<li>Copier le lien</li>
							<li>Exécuter <code>git clone https://lienCopiéSurGitHub</code> dans le dossier devant recevoir le repository</li>
						</ul>
					</td>
				</tr>
				<tr>
					<td>Créer un repository</td>
					<td>
						<ul>
    						<li>Cliquer sur le + situé à gauche de l''avatar puis sur new repository</li>
    						<li>Renseigner le nom, la description et ajouter une licence</li>
    						<li>Au passage on peut initialiser le repository avec un fichier README.md pour pouvoir immédiatement le cloner sur notre ordi. Inutile si on a déjà un repository sur l''ordi.</li>
    					</ul>
					</td>
				</tr>
                <tr>
                    <td>Connecter un repository local avec un repository GitHub</td>
                    <td>
                        <ul>
                            <li>Ouvrir une console dans le repository local</li>
                            <li>Exécuter <code>git remote add origin https://github.com/nomUtilisateur/nomProjet</code></li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td>Changer l''url du repository GitHUB</td>
                    <td>
                        <code>git remote set-url https://github.com/userName/repositoryName.git</code>
                    </td>
                </tr>
                <tr>
                    <td>Envoyer le code du repository local vers GitHub</td>
                    <td>
                        <ul>
                            <li>Ouvrir une console dans le repository local</li>
                            <li>Faire un commit des modifications apportées au code</li>
                            <li>Utiliser la commande <code>git push origin master</code> ou, si on travaille sur une branche, <code>git push origin nomDeLaBranche</code></li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td>Récupérer des modifs depuis GitHub vers le repository local</td>
                    <td>
                        <ul>
                            <li>Ouvrir une console dans le repository local</li>
                            <li>Exécuter <code>git pull origin master</code></li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td>Faire une pull request pour contribuer à un projet open source</td>
                    <td>
                        <ol>
                            <li>
                                Lire les consignes de contribution dans le <em>README.md</em> du projet
                            </li>
                            <li>
                                Sur le repository auquel on veut contribuer, faire un <em>Fork</em> en cliquant sur le bouton <em>Fork</em> du repository
                            </li>
                            <li>
                                Cloner le repository sur notre ordi via un <code>git clone</code>
                            </li>
                            <li>
                                Créer une nouvelle branche dans le repository cloné avec <code>git checkout -b nomDeLaNouvelleBranche</code>
                            </li>
                            <li>
                                Faire les modifications et faire un commit
                            </li>
                            <li>
                                Envoyer les modifications sur GitHUB avec un <code>git push origin nomDeLaNouvelleBranche</code>
                            </li>
                            <li>
                                Aller sur le repository GitHUB cloné et cliquer sur <em>Compare & pull-request</em>
                            </li>
                            <li>
                                Rédiger un joli message expliquant le pourquoi du comment et c''est parti !
                            </li>
                        </ol>
                    </td>
                </tr>
			</table>

			<h3>Astuces</h3>

			<ul id="tipsList">
				<li>touche T : Faire une recherche</li>
			</ul>
    	</article>'),
(11, 3, null, '2019-02-26 22:02:10', '2019-02-26 22:02:10', 0, 'Créer un thème enfant pour WordPress', 'Tuto rapide pour apprendre à créer un thème à partir d''un autre déjà existant.', '<article>
            <h2>Thême enfant</h2>

            <h3>Création</h3>

            <ol>
                <li>Aller dans le dossier <em>wp-content/themes</em> de Wordpress</li>
                <li>Créer un nouveau dossier (exemple : <em>nomDuThemeParent-child</em>)</li>
                <li>Copier les fichiers <em>functions.php</em> et <em>style.css</em> présents dans le dossier du thême parent et les placer dans le dossier du thême enfant</li>
                <li>
                    Faire les modifs suivantes dans les fichiers du dossier enfant :
                    <ul>
                        <li>
                            Fichier style.css
                            <ol>
                                <li>
                                    Copier le gros bloc de commentaires situé au début du fichier<br />
                                    Exemple :
                                    <pre>
/*
Theme Name: Nom du thème
Theme URI: https://wordpress.org/themes/twentyfifteen/
Author: the WordPress team
Author URI: https://wordpress.org/
Description: Voilà, c''est une description, on met ce qu''on veut. C''est cool.
Version: 2.0
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: bootstrap
Text Domain: twentyfifteen

This theme, like WordPress, is licensed under the GPL.
Use it to make something cool, have fun, and share what you''ve learned with others.
*/
                                    </pre>
                                </li>
                                <li>Changez le <em>Theme Name:</em> avec le nom du dossier du thème enfant</li>
                                <li>
                                    Ajouter la ligne suivante sous <em>Theme Name:</em> :
<pre>
Template: nomDuDossierDuThèmeParent
</pre>
                                </li>
                                <li>Effacer le contenu du fichier</li>
                                <li>Coller le gros bloc de commentaires</li>
                                <li>Enregistrer</li>
                            </ol>
                        </li>
                        <li>
                            Fichier functions.php
                            <ol>
                                <li>Effacer le contenu du fichier</li>
                                <li>
                                    Copier/coller le bloc de code suivant :
<pre>
&lt;?php
add_action( ''wp_enqueue_scripts'', ''theme_enqueue_styles'' );

function theme_enqueue_styles() {
    wp_enqueue_style( ''parent-style'', get_template_directory_uri() . ''/style.css'' );
    wp_enqueue_style( ''child-style'', get_stylesheet_uri(), array( ''parent-style'' ) );
}</pre>
                                </li>
                                <li>Enregistrer</li>
                            </ol>
                        </li>
                    </ul>
                </li>
            </ol>

            <h3>Utilisation</h3>

            <ol>
                <li>Dans le tableau de bord Wordpress : <em>Apparence/Thèmes</em></li>
                <li>Activer le thême enfant</li>
            </ol>
        </article>'),
(12, 3, null, '2019-02-26 22:10:12', '2019-02-26 22:10:12', 0, 'Créer un thème WordPress à partir de rien', 'Cette fois-ci un tuto pour créer un thème de zéro. Pour des explications plus détaillées, vous pouvez vous rendre sur le blog de Tania Rascia (https://www.taniarascia.com/).', '<article>
            <h2>Création d''un thème WordPress</h2>

            <p>
                Tuto repris du site de <a href="https://www.taniarascia.com/developing-a-wordpress-theme-from-scratch/" target="_blank">Tania Rascia</a>. J''ai juste synthétisé les modifs que l''auteure fait au fur et à mesure de son tuto qui est du coup plus détaillé et progressif.
            </p>

            <!-- Version statique -->
            <article>
                <h3>Version HTML statique (pour comprendre la base)</h3>

                <p>
                    <ol>
                        <li>Créer un dossier portant le nom du thème à créer dans le dossier <em>wp-content/themes</em> de Wordpress</li>
                        <li>Créer 2 fichiers
                            <ul>
                                <li><em>index.php</em></li>
                                <li><em>style.css</em>
                                </li>
                            </ul>
                        </li>
                    </ol>
                    <p>
                        Le fichier index.php est constitué de votre page HTML statique.
                    </p>
                    <p>Contenu du fichier <em>style.css</em> (à personnaliser) :
<pre>
/*
Theme Name: Nom du thème
Author: Votre nom
Description: Alors c''est un super thème, joli et tout et tout...
Version: 0.0.1
Tags: bootstrap
*/
</pre>
                    </p>
                    <p class="note">
                        Ces 2 fichiers suffisent pour voir apparaître votre thème dans l''interface de WordPress. Vous avez réussi ! Bravo !
                    </p>
                    <p>
                        Vous pouvez aussi ajouter un autre fichier css dans le dossier de votre thème pour la mise en forme de votre site. Pensez alors à mettre un lien dans le <code>&lt;head&gt;</code> du fichier <em>index.php</em> (exemple <code>&lt;link href="blog.css" rel="stylesheet"&gt;</code>).
                    </p>
                    <p>
                        Voici maintenant <a href="https://github.com/taniarascia/bootstrapblog">un lien vers un repository GitHub de Tania Rascia</a> où vous trouverez 2 fichiers pour vous entraîner et qui serviront de base à la suite de ce tuto. N''oubliez pas de renommer le fichier <em>index.html</em> en <em>index.php</em> ni de créer le fichier <em>style.css</em> !
                    </p>
                    <p class="note">Conclusion : c''est pas compliqué mais pour l''instant le résultat n''est pas ouf non plus... Passons maintenant aux choses sérieuses !</p>
                </p>
            </article>

            <!-- Version dynamique -->
            <article>
                <h3>Version dynamique !</h3>

                <p>
                    On va diviser le contenu du fichier <em>index.php</em> dans 4 fichiers : <em>header.php</em>, <em>footer.php</em>, <em>sidebar.php</em> et <em>content.php</em>
                </p>
                <p>
                    Le fichier <em>index.php</em> servira alors de lien entre ces fichiers.
                </p>
                <p>C''est parti !</p>

                <!-- header.php -->
                <h4>header.php</h4>
<pre>
&lt;!DOCTYPE html>
&lt;html lang="en">

&lt;head>
    &lt;meta charset="utf-8">
    &lt;meta http-equiv="X-UA-Compatible" content="IE=edge">
    &lt;meta name="viewport" content="width=device-width, initial-scale=1">
    &lt;meta name="description" content="">
    &lt;meta name="author" content="">

    &lt;title><span class="php_code">&lt;?php echo get_bloginfo( ''name'' ); ?></span>&lt;/title>
    &lt;link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
    &lt;!-- Custom CSS -->
    &lt;link href="<span class="php_code">&lt;?php echo get_bloginfo( ''template_directory'' );?></span>/blog.css" rel="stylesheet">
    &lt;!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    &lt;!--[if lt IE 9]>
        &lt;script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js">&lt;/script>
        &lt;script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js">&lt;/script>
    &lt;![endif]-->
&lt;?php wp_head();?>
&lt;/head>

&lt;body>

    &lt;div class="blog-masthead">
        &lt;div class="container">
            &lt;nav class="blog-nav">
                &lt;a class="blog-nav-item active" href="#">Home&lt;/a>
                <span class="php_code">&lt;?php wp_list_pages( ''&title_li='' ); ?></span>
            &lt;/nav>
        &lt;/div>
    &lt;/div>

    &lt;div class="container">

    &lt;div class="blog-header">
        &lt;h1 class="blog-title">&lt;a href="&lt;?php echo get_bloginfo( ''wpurl'' );?>">&lt;?php echo get_bloginfo( ''name'' ); ?>&lt;/a>&lt;/h1>
        &lt;p class="lead blog-description">&lt;?php echo get_bloginfo( ''description'' ); ?>&lt;/p>
    &lt;/div>
</pre>
                <p>
                    La fonction php <code><span class="php_code">&lt;?php echo get_bloginfo( ''name'' ); ?></span></code> va permettre d''insérer le titre du site que vous avez choisi dans l''interface d''administration de WordPress.
                </p>
                <p>
                    La fonction php <code><span class="php_code">&lt;?php echo get_bloginfo( ''template_directory'' );?></span></code> va elle insérer automatiquement le chemin d''accès au dossier de votre thème.
                </p>
                <p>
                    La fonction php <code><span class="php_code">&lt;?php wp_list_pages( ''&title_li='' ); ?></span></code> va insérer les liens vers les différentes pages du site.
                </p>
                <p class="note">Les liens vont mal s''afficher en utilisant le fichier <em>blog.css</em> du repo de Tanya. Il faut alors ajouter le code suivant dans le fichier <em>blog.css</em> :</p>
<pre>
.blog-nav li {
    position: relative;
    display: inline-block;
    padding: 10px;
    font-weight: 500;
}
.blog-nav li a {
    color: #fff;
}
</pre>

                <!-- footer.php -->
                <h4>footer.php</h4>
<pre>
        &lt;/div> &lt;!-- /.container -->

        &lt;footer class="blog-footer">
            &lt;p>Blog template built for &lt;a href="http://getbootstrap.com">Bootstrap&lt;/a> by &lt;a href="https://twitter.com/mdo">@mdo&lt;/a>.&lt;/p>

            &lt;p>&lt;a href="#">Back to top&lt;/a>&lt;/p>
        &lt;/footer>

        &lt;script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js">&lt;/script>
        &lt;script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js">&lt;/script>
        &lt;?php wp_footer(); ?>
    &lt;/body>
&lt;/html>
</pre>

                <!-- sidebar.php -->
                <h4>sidebar.php</h4>
<pre>
&lt;div class="col-sm-3 col-sm-offset-1 blog-sidebar">

    &lt;div class="sidebar-module sidebar-module-inset">

        &lt;h4>About&lt;/h4>

        &lt;p><span class="php_code">&lt;?php the_author_meta( ''description'' ); ?></span> &lt;/p>
    &lt;/div>

    &lt;div class="sidebar-module">

        &lt;h4>Archives&lt;/h4>

        &lt;ol class="list-unstyled">
            <span class="php_code">&lt;?php wp_get_archives( ''type=monthly'' ); ?></span>
        &lt;/ol>
    &lt;/div>

    &lt;div class="sidebar-module">

        &lt;h4>Elsewhere&lt;/h4>

        &lt;ol class="list-unstyled">
            &lt;li>&lt;a href="#">GitHub&lt;/a>&lt;/li>
            &lt;li>&lt;a href="#">Twitter&lt;/a>&lt;/li>
            &lt;li>&lt;a href="#">Facebook&lt;/a>&lt;/li>
        &lt;/ol>
    &lt;/div>
&lt;/div>&lt;!-- /.blog-sidebar -->
</pre>
                <p>
                    La fonction <code><span class="php_code">&lt;?php the_author_meta( ''description'' ); ?></span></code> va afficher la description de l''auteur.
                </p>
                <p>
                    La fonction <code><span class="php_code">&lt;?php wp_get_archives( ''type=monthly'' ); ?></span></code> va regrouper les archives triées par mois.
                </p>

                <!-- content.php -->
                <h4>content.php</h4>
<pre>
&lt;div class="blog-post">
    &lt;!-- Titre du billet de blog -->
    &lt;h2 class="blog-post-title"><span class="php_code">&lt;?php the_title(); ?></span>&lt;/h2>

    &lt;!-- Auteur et date du billet de blog -->
    &lt;p class="blog-post-meta"><span class="php_code">&lt;?php the_date(); ?></span> by &lt;a href="#"><span class="php_code">&lt;?php the_author(); ?></span>&lt;/a>&lt;/p>

    &lt;!-- Contenu du billet de blog -->
    <span class="php_code">&lt;?php the_content(); ?></span>
&lt;/div>&lt;!-- /.blog-post -->
</pre>
                <p>
                    La fonction <code><span class="php_code">&lt;?php the_title(); ?></span></code> permet d''afficher le titre du billet de blog.
                </p>
                <p>
                    La fonction <code><span class="php_code">&lt;?php the_date(); ?></span></code> affiche la date de création du billet de blog.
                </p>
                <p>
                    La fonction <code><span class="php_code">&lt;?php the_author(); ?></span></code> affiche l''auteur du billet.
                </p>
                <p>
                    La fonction <code><span class="php_code">&lt;?php the_content(); ?></span></code> affiche le contenu du billet.
                </p>

                <!-- index.php -->
                <h4>index.php</h4>

<pre>
<span class="php_code">&lt;?php get_header(); ?></span>

    &lt;div class="row">

        &lt;div class="col-sm-8 blog-main">
            <span class="php_code">&lt;?php
            if ( have_posts() ) : while ( have_posts() ) : the_post();
                get_template_part( ''content'', get_post_format() );
            endwhile; endif;
            ?></span>
        &lt;/div> &lt;!-- /.blog-main -->

        <span class="php_code">&lt;?php get_sidebar(); ?></span>

    &lt;/div> &lt;!-- /.row -->

<span class="php_code">&lt;?php get_footer(); ?></span>
</pre>
                <p>
                    Les instructions suivantes servent à générer les articles de blogs disponibles :
<pre>
    <span class="php_code">&lt;?php
    if ( have_posts() ) : while ( have_posts() ) : the_post();
        get_template_part( ''content'', get_post_format() );
    endwhile; endif;
    ?></span>
</pre>
                </p>
                <p>
                    Les instructions <code><span class="php_code">&lt;?php get_header(); ?></span></code>, <code><span class="php_code">&lt;?php get_sidebar(); ?></span></code> et <code><span class="php_code">&lt;?php get_footer(); ?></span></code> servent à insérer le code contenu dans les fichiers <em>header.php</em>, <em>sidebar.php</em> et <em>footer.php</em>.
                </p>
                <p class="note">
                    Essayez maintenant d''ajouter des articles de blog, de changer le nom et le slogan du site via l''interface WordPress et admirez le résultat !
                </p>
            </article>

            <!-- Création d''un affichage différent pour les pages du site -->
            <article>
                <h3>On se pose en douceur... Création d''un affichage différent pour les pages du site</h3>

                <p>
                    Il faut créer un fichier <em>page.php</em> qui va ressembler fortement à <em>index.php</em>.
                </p>
                <p>
                    Dans l''exemple suivant, on a choisit de ne pas afficher la barre latérale (en omettant le fichier <em>sidebar.php</em>) et d''afficher le contenu des pages sur toutes la largeur (avec la classe Bootstrap <em>col-sm-12</em>).
                </p>
<pre>
<span class="php_code">&lt;?php get_header(); ?></span>

    &lt;div class="row">
        &lt;div class="col-sm-12">

            <span class="php_code">&lt;?php
                if ( have_posts() ) : while ( have_posts() ) : the_post();

                    get_template_part( ''content'', get_post_format() );

                endwhile; endif;
            ?></span>

        &lt;/div> &lt;!-- /.col -->
    &lt;/div> &lt;!-- /.row -->

<span class="php_code">&lt;?php get_footer(); ?></span>
</pre>
                <p class="note">
                    Un grand merci à Tania Rascia pour avoir fait le tuto sur lequel j''ai honteusement pompé. <a href="https://ko-fi.com/taniarascia">Vous pouvez d''ailleurs lui payer un café pour la remercier.</a>
                </p>
            </article>
        </article>');


INSERT INTO bl_post_tag (pt_post_id_fk, pt_tag_id_fk)
VALUES
(1, 2),
(2, 2),
(3, 2),
(4, 3),
(5, 3),
(6, 3),
(7, 3),
(8, 9),
(8, 5),
(8, 7),
(9, 10),
(9, 5),
(9, 7),
(10, 12),
(10, 5),
(10, 7),
(11, 5),
(11, 7),
(11, 14),
(11, 15),
(12, 5),
(12, 7),
(12, 14),
(12, 15);

INSERT INTO bl_post_category (pc_post_id_fk, pc_category_id_fk)
VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 2),
(5, 2),
(6, 3),
(7, 3),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1);

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