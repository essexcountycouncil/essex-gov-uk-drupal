#!/bin/bash
drush mim ecc_users && \
drush mim ecc_files && \
drush mim ecc_image_media && \
drush mim ecc_document_media && \
drush mim ecc_youtube_embeds && \
drush mim ecc_newsrooms && \
drush mim ecc_news && \
drush mim ecc_guide_pages && \
drush mim ecc_guide_overviews && \
drush mim ecc_service_pages && \
drush mim ecc_service_landing_pages && \
drush mim ecc_nested_redirects
