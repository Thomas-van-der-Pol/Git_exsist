CREATE TABLE [dbo].[CRM_RELATION_ADDRESS]
(
[ID] [int] NOT NULL IDENTITY(1, 1),
[ACTIVE] [bit] NOT NULL CONSTRAINT [DF_CRM_RELATION_ADDRESS_ACTIVE] DEFAULT ((1)),
[TS_CREATED] [datetime] NOT NULL CONSTRAINT [DF_CRM_RELATION_ADDRESS_TS_CREATED] DEFAULT (getdate()),
[TS_LASTMODIFIED] [datetime] NULL CONSTRAINT [DF_CRM_RELATION_ADDRESS_TS_LASTMODIFIED] DEFAULT (getdate()),
[FK_CRM_RELATION] [int] NULL,
[FK_CORE_ADDRESS] [int] NULL,
[FK_CORE_DROPDOWNVALUE_ADRESSTYPE] [int] NULL,
[EXACT_ADDRESS_ID] [nvarchar] (255) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[EXACT_ADDRESS_LASTSYNC] [datetime] NULL,
[EXACT_ADDRESS_ERROR] [nvarchar] (max) COLLATE SQL_Latin1_General_CP1_CI_AS NULL
) ON [PRIMARY]
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO


CREATE TRIGGER [dbo].[TR_CRM_RELATION_ADDRESS_TS_LASTMODIFIED]
		   ON  [dbo].[CRM_RELATION_ADDRESS]
		   AFTER UPDATE
		AS 
		BEGIN
			SET NOCOUNT ON;

			UPDATE T SET 
				T.TS_LASTMODIFIED = GETDATE()
			FROM 
				CRM_RELATION_ADDRESS  T
			INNER JOIN INSERTED I ON T.ID = I.ID
		END
GO
ALTER TABLE [dbo].[CRM_RELATION_ADDRESS] ADD CONSTRAINT [PK_CRM_RELATION_ADDRESS] PRIMARY KEY CLUSTERED  ([ID]) ON [PRIMARY]
GO
ALTER TABLE [dbo].[CRM_RELATION_ADDRESS] ADD CONSTRAINT [FK_CRM_RELATION_ADDRESS_CORE_ADDRESS] FOREIGN KEY ([FK_CORE_ADDRESS]) REFERENCES [dbo].[CORE_ADDRESS] ([ID])
GO
ALTER TABLE [dbo].[CRM_RELATION_ADDRESS] ADD CONSTRAINT [FK_CRM_RELATION_ADDRESS_CORE_DROPDOWNVALUE] FOREIGN KEY ([FK_CORE_DROPDOWNVALUE_ADRESSTYPE]) REFERENCES [dbo].[CORE_DROPDOWNVALUE] ([ID])
GO
ALTER TABLE [dbo].[CRM_RELATION_ADDRESS] ADD CONSTRAINT [FK_CRM_RELATION_ADDRESS_CRM_RELATION] FOREIGN KEY ([FK_CRM_RELATION]) REFERENCES [dbo].[CRM_RELATION] ([ID])
GO
