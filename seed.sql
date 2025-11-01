-- Seed data for initial setup

-- Default settings
INSERT INTO `settings` (`k`, `v`) VALUES
('global_on', '1'),
('default_template', '🔖 {tags}\n🆔 {ids}\n\nPost: {message_id} · {date}\n{permalink}'),
('tag_separator', ' · '),
('id_separator', ' | '),
('parse_mode', 'MarkdownV2'),
('locale', 'fa'),
('rate_limit_per_minute', '20')
ON DUPLICATE KEY UPDATE `v` = VALUES(`v`);

-- Persian template alternative
INSERT INTO `settings` (`k`, `v`) VALUES
('default_template_fa', '🔖 {tags}\n🆔 {ids}\n\nپست: {message_id} · {date}\n{permalink}')
ON DUPLICATE KEY UPDATE `v` = VALUES(`v`);

-- Install flag (will be set to 1 after installation)
INSERT INTO `install_flag` (`installed`) VALUES (0);

