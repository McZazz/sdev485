CREATE TABLE adviseit_tokens (
  token varchar(6) NOT NULL,
  created datetime DEFAULT NULL,
  last_saved datetime DEFAULT NULL,
  advisor varchar(50) DEFAULT NULL,
  saved tinyint(1) NOT NULL,
  PRIMARY KEY (token)
);

CREATE TABLE adviseit_2020 (
  token varchar(6) NOT NULL,
  fall varchar(1000) DEFAULT NULL,
  winter varchar(1000) DEFAULT NULL,
  spring varchar(1000) DEFAULT NULL,
  summer varchar(1000) DEFAULT NULL,
  PRIMARY KEY (token)
);