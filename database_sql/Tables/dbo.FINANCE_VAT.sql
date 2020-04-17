CREATE TABLE [dbo].[FINANCE_VAT]
(
[ID] [int] NOT NULL IDENTITY(1, 1),
[TS_CREATED] [datetime] NOT NULL CONSTRAINT [DF_FINANCE_VAT_TS_CREATED] DEFAULT (getdate()),
[TS_LASTMODIFIED] [datetime] NULL CONSTRAINT [DF_FINANCE_VAT_TS_LASTMODIFIED] DEFAULT (getdate()),
[ACTIVE] [bit] NOT NULL CONSTRAINT [DF_FINANCE_VAT_ACTIVE] DEFAULT ((1)),
[FK_CORE_LABEL] [int] NOT NULL,
[VATCODE] [int] NULL,
[DESCRIPTION] [nvarchar] (255) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[PERCENTAGE] [decimal] (30, 15) NULL
) ON [PRIMARY]
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO
CREATE TRIGGER [dbo].[TR_FINANCE_VAT_TS_LASTMODIFIED]
						   ON  [dbo].[FINANCE_VAT]
						   AFTER UPDATE
						AS 
						BEGIN
							SET NOCOUNT ON;

							UPDATE T SET 
								T.TS_LASTMODIFIED = GETDATE()
							FROM 
								FINANCE_VAT T
							INNER JOIN INSERTED I ON T.ID = I.ID
						END
GO
ALTER TABLE [dbo].[FINANCE_VAT] ADD CONSTRAINT [PK_FINANCE_VAT] PRIMARY KEY CLUSTERED  ([ID]) ON [PRIMARY]
GO
ALTER TABLE [dbo].[FINANCE_VAT] ADD CONSTRAINT [FK_FINANCE_VAT_CORE_LABEL] FOREIGN KEY ([FK_CORE_LABEL]) REFERENCES [dbo].[CORE_LABEL] ([ID])
GO
