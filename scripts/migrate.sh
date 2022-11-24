#!/bin/bash
drush migrate:import ecc_content_owners && \
drush migrate:import ecc_users && \
drush migrate:import ecc_files && \
drush migrate:import ecc_image_media && \
drush migrate:import ecc_document_media && \
drush migrate:import ecc_youtube_embeds && \
drush migrate:import ecc_newsrooms && \
drush migrate:import ecc_news && \
drush migrate:import ecc_guide_pages && \
drush migrate:import ecc_guide_overviews && \
drush migrate:import ecc_service_pages && \
drush migrate:import ecc_service_landing_pages && \
drush migrate:import ecc_nested_redirects && \
drush migrate:import ecc_301_redirects && \
drush migrate:import ecc_302_redirects
