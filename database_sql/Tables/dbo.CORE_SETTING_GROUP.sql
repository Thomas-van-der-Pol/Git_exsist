CREATE TABLE [dbo].[CORE_SETTING_GROUP]
(
[ID] [int] NOT NULL IDENTITY(1, 1),
[ACTIVE] [bit] NOT NULL CONSTRAINT [DF_CORE_SETTING_GROUP_ACTIVE] DEFAULT ((1)),
[SEQUENCE] [float] NULL,
[TS_CREATED] [datetime] NOT NULL CONSTRAINT [DF_CORE_SETTING_GROUP_TS_CREATED] DEFAULT (getdate()),
[TS_LASTMODIFIED] [datetime] NULL CONSTRAINT [DF_CORE_SETTING_GROUP_TS_LASTMODIFIED] DEFAULT (getdate()),
[DESCRIPTION] [nvarchar] (255) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
[VISIBLE] [bit] NULL CONSTRAINT [DF_CORE_SETTING_GROUP_VISIBLE] DEFAULT ((1))
) ON [PRIMARY]
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO

CREATE TRIGGER [dbo].[TR_CORE_SETTING_GROUP_TS_LASTMODIFIED]
	ON  [dbo].[CORE_SETTING_GROUP]
	AFTER UPDATE
AS 
BEGIN
	SET NOCOUNT ON;

	UPDATE T SET 
		T.TS_LASTMODIFIED = GETDATE()
	FROM 
		CORE_SETTING_GROUP T
	INNER JOIN INSERTED I ON T.ID = I.ID
END
GO
ALTER TABLE [dbo].[CORE_SETTING_GROUP] ADD CONSTRAINT [PK_CORE_SETTING_GROUP] PRIMARY KEY CLUSTERED  ([ID]) ON [PRIMARY]
GO
