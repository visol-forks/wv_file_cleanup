#
# Additional field for table 'sys_file'
#
CREATE TABLE sys_file (
  last_move int(11) DEFAULT '0' NOT NULL
);

#
# Additional field for table 'sys_file_metadata'
#
CREATE TABLE sys_file_metadata (
  cleanup_protected tinyint(1) unsigned DEFAULT '0' NOT NULL
);
