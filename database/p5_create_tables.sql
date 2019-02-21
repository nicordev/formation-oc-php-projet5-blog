
DROP TABLE IF EXISTS bl_role_member;
DROP TABLE IF EXISTS bl_category_tag;
DROP TABLE IF EXISTS bl_post_tag;

DROP TABLE IF EXISTS bl_comment;
DROP TABLE IF EXISTS bl_post;
DROP TABLE IF EXISTS bl_member;
DROP TABLE IF EXISTS bl_role;
DROP TABLE IF EXISTS bl_category;
DROP TABLE IF EXISTS bl_tag;

DROP TABLE IF EXISTS bl_key;

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


