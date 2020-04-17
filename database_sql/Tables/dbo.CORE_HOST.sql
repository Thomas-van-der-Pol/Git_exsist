CREATE TABLE [dbo].[CORE_HOST]
(
[ID] [int] NOT NULL IDENTITY(1, 1),
[ACTIVE] [bit] NOT NULL CONSTRAINT [DF_CORE_HOSTS_ACTIVE] DEFAULT ((1)),
[TS_CREATED] [datetime] NOT NULL CONSTRAINT [DF_CORE_HOSTS_TS_CREATED] DEFAULT (getdate()),
[TS_LASTMODIFIED] [datetime] NULL CONSTRAINT [DF_CORE_HOSTS_TS_LASTMODIFIED] DEFAULT (getdate()),
[MAC_ADDRESS] [nvarchar] (500) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[HOSTNAME] [nvarchar] (255) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[PRINTER_DEFAULT] [nvarchar] (500) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[PRINTER_INVOICE] [nvarchar] (500) COLLATE SQL_Latin1_General_CP1_CI_AS NULL
) ON [PRIMARY]
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO

CREATE TRIGGER [dbo].[TR_CORE_HOST_TS_LASTMODIFIED]
		   ON  [dbo].[CORE_HOST]
		   AFTER UPDATE
		AS 
		BEGIN
			SET NOCOUNT ON;

			UPDATE T SET 
				T.TS_LASTMODIFIED = GETDATE()
			FROM 
				CORE_HOST  T
			INNER JOIN INSERTED I ON T.ID = I.ID
		END
GO
ALTER TABLE [dbo].[CORE_HOST] ADD CONSTRAINT [PK_CORE_HOST] PRIMARY KEY CLUSTERED  ([ID]) ON [PRIMARY]
GO
