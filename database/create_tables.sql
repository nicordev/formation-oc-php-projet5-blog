
DROP TABLE IF EXISTS bl_role_member;
DROP TABLE IF EXISTS bl_post_category;
DROP TABLE IF EXISTS bl_comment;
DROP TABLE IF EXISTS bl_post;
DROP TABLE IF EXISTS bl_member;
DROP TABLE IF EXISTS bl_language;
DROP TABLE IF EXISTS bl_role;
DROP TABLE IF EXISTS bl_category;

CREATE TABLE bl_language(
	l_name VARCHAR(100),

	PRIMARY KEY (l_name)
)
ENGINE = InnoDB;

CREATE TABLE bl_role(
	r_name VARCHAR(100),

	PRIMARY KEY (r_name)
)
ENGINE = InnoDB;

CREATE TABLE bl_member(
	m_id INT UNSIGNED AUTO_INCREMENT,
	m_name VARCHAR(100) NOT NULL,
	m_email VARCHAR(100) NOT NULL,
	m_password VARCHAR(100) NOT NULL,
	m_website VARCHAR(100) NOT NULL,
	m_language VARCHAR(100) NOT NULL DEFAULT 'Français',

	PRIMARY KEY (m_id),
	CONSTRAINT fk_m_language_l_name
		FOREIGN KEY (m_language)
			REFERENCES bl_language(l_name)
				ON UPDATE CASCADE
				ON DELETE CASCADE
)
ENGINE = InnoDB;

CREATE TABLE bl_role_member(
	rm_member_id INT UNSIGNED,
	rm_role_name VARCHAR(100),

	PRIMARY KEY (rm_member_id, rm_role_name),
	CONSTRAINT fk_rm_member_id_bl_m_id
		FOREIGN KEY (rm_member_id)
			REFERENCES bl_member(m_id)
				ON UPDATE CASCADE
				ON DELETE CASCADE
)
ENGINE = InnoDB;

CREATE TABLE bl_post(
	p_id INT UNSIGNED AUTO_INCREMENT,
	p_author_id INT UNSIGNED,
	p_chapo VARCHAR(300) NOT NULL,
	p_content TEXT NOT NULL,
	p_creation_date DATETIME NOT NULL,
	p_modification_date DATETIME,

	PRIMARY KEY (p_id),
	CONSTRAINT fk_p_author_id_m_id
		FOREIGN KEY (p_author_id)
			REFERENCES bl_member(m_id)
				ON UPDATE CASCADE
				ON DELETE CASCADE
)
ENGINE = InnoDB;

CREATE TABLE bl_category(
	c_name VARCHAR(100),

	PRIMARY KEY (c_name)
)
ENGINE = InnoDB;

CREATE TABLE bl_post_category(
	pc_post_id INT UNSIGNED,
	pc_category_name VARCHAR(100),

	PRIMARY KEY (pc_post_id, pc_category_name),
	CONSTRAINT fk_pc_post_id_p_id
		FOREIGN KEY (pc_post_id)
			REFERENCES bl_post(p_id)
				ON UPDATE CASCADE
				ON DELETE CASCADE,
	CONSTRAINT fk_pc_category_name_c_name
		FOREIGN KEY (pc_category_name)
			REFERENCES bl_category(c_name)
				ON UPDATE CASCADE
				ON DELETE CASCADE
)
ENGINE = InnoDB;

CREATE TABLE bl_comment(
	co_id INT UNSIGNED AUTO_INCREMENT,
	co_author_id INT UNSIGNED,
	co_post_id INT UNSIGNED,
	co_creation_date DATETIME NOT NULL,
	co_content TEXT,

	PRIMARY KEY (co_id),
	CONSTRAINT fk_co_author_id_m_id
		FOREIGN KEY (co_author_id)
			REFERENCES bl_member(m_id)
				ON UPDATE CASCADE
				ON DELETE CASCADE,
	CONSTRAINT fk_co_post_id_p_id
		FOREIGN KEY (co_post_id)
			REFERENCES bl_post(p_id)
				ON UPDATE CASCADE
				ON DELETE CASCADE
)
ENGINE = InnoDB;
