CREATE TABLE [dbo].[FINANCE_INVOICE_COLLECT_INTERVAL]
(
[ID] [int] NOT NULL IDENTITY(1, 1),
[TS_CREATED] [datetime] NOT NULL CONSTRAINT [DF_FINANCE_INVOICE_COLLECT_INTERVAL_TS_CREATED] DEFAULT (getdate()),
[TS_LASTMODIFIED] [datetime] NULL CONSTRAINT [DF_FINANCE_INVOICE_COLLECT_INTERVAL_TS_LASTMODIFIED] DEFAULT (getdate()),
[ACTIVE] [bit] NOT NULL CONSTRAINT [DF_FINANCE_INVOICE_COLLECT_INTERVAL_ACTIVE] DEFAULT ((1)),
[SEQUENCE] [int] NULL,
[DESCRIPTION] [nvarchar] (500) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[DESCRIPTION_SHORT] [nvarchar] (500) COLLATE SQL_Latin1_General_CP1_CI_AS NULL
) ON [PRIMARY]
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO
CREATE TRIGGER [dbo].[TR_FINANCE_INVOICE_COLLECT_INTERVAL_TS_LASTMODIFIED]
						   ON  [dbo].[FINANCE_INVOICE_COLLECT_INTERVAL]
						   AFTER UPDATE
						AS 
						BEGIN
							SET NOCOUNT ON;

							UPDATE T SET 
								T.TS_LASTMODIFIED = GETDATE()
							FROM 
								FINANCE_INVOICE_COLLECT_INTERVAL T
							INNER JOIN INSERTED I ON T.ID = I.ID
						END
GO
ALTER TABLE [dbo].[FINANCE_INVOICE_COLLECT_INTERVAL] ADD CONSTRAINT [PK_FINANCE_INVOICE_COLLECT_INTERVAL] PRIMARY KEY CLUSTERED  ([ID]) ON [PRIMARY]
GO
