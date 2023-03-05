

CREATE TABLE info (
id INT NOT NULL IDENTITY(1,1) PRIMARY KEY,
[temp] float NULL,
[humidade] float NULL,
[sensorLuz] bit NULL,
[luzes] bit NULL,
[aquecimento] bit NULL,
[telhado] bit NULL,
[reading_time] datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
)





