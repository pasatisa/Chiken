USE [chiken]
GO

/****** Object:  Table [dbo].[info]    Script Date: 04/03/2023 11:57:27 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[info](
	[id] [int] NOT NULL,
	[dia] [date] NULL,
	[horas] [time](7) NULL,
	[temp] [float] NULL,
	[humidade] [float] NULL,
	[sensorLuz] [bit] NULL,
	[luzes] [bit] NULL,
	[aquecimento] [bit] NULL,
	[telhado] [bit] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO

