
SELECT DISTINCT ct_category_id_fk FROM bl_category_tag
WHERE ct_tag_id_fk IN (
    SELECT pt_tag_id_fk FROM bl_post_tag
    WHERE pt_post_id_fk = 23
)
;

SELECT * FROM bl_post
WHERE p_id IN (
    SELECT DISTINCT pt_post_id_fk FROM bl_post_tag
    WHERE pt_tag_id_fk IN (
        SELECT tag_id FROM bl_tag
            INNER JOIN bl_category_tag
                ON tag_id = ct_tag_id_fk
            INNER JOIN bl_category
                ON cat_id = ct_category_id_fk
        WHERE cat_id = :id) # Use the requested category id here
)
;