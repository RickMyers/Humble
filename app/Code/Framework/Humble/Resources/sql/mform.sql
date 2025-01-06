             SELECT a.id, a.id AS form_id, a.created_by, a.created, a.submitted, a.last_activity, a.review_by, a.member_id, a.status, a.member_name, b.id as creator, a.event_date, a.event_time, a.tag,
                    b.first_name AS creator_first_name, b.last_name AS creator_last_name, b.gender as creator_gender, a.form_type, a.status, a.claim_status, a.screening_client,
                    concat(c.first_name,' ',c.last_name) as technician_name, a.last_action, a.address_id, a.phonetic_token1, a.phonetic_token2, a.reviewer, concat(d.first_name,' ',d.last_name) as reviewer_name, 
                    a.pcp_portal_withhold, a.location_id_combo, a.address_id_combo, a.event_id
              FROM vision_consultation_forms AS a
              LEFT OUTER JOIN humble_user_identification AS b
                ON a.created_by = b.id
              left outer join humble_user_identification as c
                on a.technician = c.id           
              left outer join humble_user_identification as d
                on a.reviewer = d.id    
             where a.id is not null
               and a.member_name like '%%last_name%%%'
               and a.status = '%%status%%'
               and a.event_date between '%%start_date%%' and '%%end_date%%'
               and a.id = '%%form_id%%'
               and a.event_date = '%%event_date%%'
