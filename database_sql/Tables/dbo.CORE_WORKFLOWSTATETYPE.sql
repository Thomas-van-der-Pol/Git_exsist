CREATE TABLE [dbo].[CORE_WORKFLOWSTATETYPE]
(
[ID] [int] NOT NULL IDENTITY(1, 1),
[ACTIVE] [bit] NOT NULL CONSTRAINT [DF_CORE_WORKFLOWSTATETYPE_ACTIVE] DEFAULT ((1)),
[TS_CREATED] [datetime] NOT NULL CONSTRAINT [DF_CORE_WORKFLOWSTATETYPE_TS_CREATED] DEFAULT (getdate()),
[TS_LASTMODIFIED] [datetime] NULL CONSTRAINT [DF_CORE_WORKFLOWSTATETYPE_TS_LASTMODIFIED] DEFAULT (getdate()),
[DESCRIPTION] [nvarchar] (255) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[FK_CORE_DROPDOWNVALUE] [int] NULL,
[FK_CORE_WORKFLOWSTATE_INVOICE] [int] NULL,
[FIXED] [bit] NULL CONSTRAINT [DF_CORE_WORKFLOWSTATETYPE_FIXED] DEFAULT ((0))
) ON [PRIMARY]
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO
CREATE TRIGGER [dbo].[TR_CORE_WORKFLOWSTATETYPE_TS_LASTMODIFIED]
		   ON  [dbo].[CORE_WORKFLOWSTATETYPE]
		   AFTER UPDATE
		AS 
		BEGIN
			SET NOCOUNT ON;

			UPDATE T SET 
				T.TS_LASTMODIFIED = GETDATE()
			FROM 
				CORE_WORKFLOWSTATETYPE  T
			INNER JOIN INSERTED I ON T.ID = I.ID
		END
GO
ALTER TABLE [dbo].[CORE_WORKFLOWSTATETYPE] ADD CONSTRAINT [PK_CORE_WORKFLOWSTATETYPE] PRIMARY KEY CLUSTERED  ([ID]) ON [PRIMARY]
GO
ALTER TABLE [dbo].[CORE_WORKFLOWSTATETYPE] ADD CONSTRAINT [FK_CORE_WORKFLOWSTATETYPE_CORE_DROPDOWNVALUE] FOREIGN KEY ([FK_CORE_DROPDOWNVALUE]) REFERENCES [dbo].[CORE_DROPDOWNVALUE] ([ID])
GO
ALTER TABLE [dbo].[CORE_WORKFLOWSTATETYPE] ADD CONSTRAINT [FK_CORE_WORKFLOWSTATETYPE_CORE_WORKFLOWSTATE] FOREIGN KEY ([FK_CORE_WORKFLOWSTATE_INVOICE]) REFERENCES [dbo].[CORE_WORKFLOWSTATE] ([ID])
GO
