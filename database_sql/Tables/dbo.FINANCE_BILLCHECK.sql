CREATE TABLE [dbo].[FINANCE_BILLCHECK]
(
[ID] [int] NOT NULL IDENTITY(1, 1),
[TS_CREATED] [datetime] NOT NULL CONSTRAINT [DF_FINANCE_BILLCHECK_TS_CREATED] DEFAULT (getdate()),
[TS_LASTMODIFIED] [datetime] NULL CONSTRAINT [DF_FINANCE_BILLCHECK_TS_LASTMODIFIED] DEFAULT (getdate()),
[ACTIVE] [bit] NOT NULL CONSTRAINT [DF_FINANCE_BILLCHECK_ACTIVE] DEFAULT ((1)),
[FK_CORE_LABEL] [int] NULL,
[FK_PROJECT] [int] NULL,
[FK_CRM_RELATION] [int] NULL,
[FK_CRM_RELATION_ADDRESS] [int] NULL,
[FK_CRM_CONTACT] [int] NULL,
[FK_FINANCE_VAT] [int] NULL,
[FK_FINANCE_LEDGER] [int] NULL,
[FK_FINANCE_INVOICE_SCHEME] [int] NULL,
[FK_FINANCE_INVOICE_COLLECT_INTERVAL] [int] NULL,
[FK_FINANCE_INVOICE_TARGET] [int] NULL,
[STARTDATE] [datetime] NULL,
[ENDDATE] [datetime] NULL,
[QUANTITY] [decimal] (15, 6) NULL,
[QUANTITY_MONTH] [decimal] (15, 6) NULL,
[DESCRIPTION] [nvarchar] (4000) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[DESCRIPTION_SPECIFICATION] [nvarchar] (4000) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[SHOW_ON_SPECIFICATION] [bit] NULL CONSTRAINT [DF_FINANCE_BILLCHECK_SHOW_ON_SPECIFICATION] DEFAULT ((0)),
[PRICE] [decimal] (15, 6) NULL,
[PRICE_TOTAL] [decimal] (15, 6) NULL,
[PRICE_INCVAT] [decimal] (15, 6) NULL,
[PRICE_TOTAL_INCVAT] [decimal] (15, 6) NULL,
[VAT_TOTAL] [decimal] (15, 6) NULL,
[DO_BILL] [bit] NULL CONSTRAINT [DF_FINANCE_BILLCHECK_DOBILL] DEFAULT ((0)),
[IS_PROJECT_INVOICE] [bit] NULL CONSTRAINT [DF_FINANCE_BILLCHECK_IS_PROJECT_INVOICE] DEFAULT ((0))
) ON [PRIMARY]
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO
CREATE TRIGGER [dbo].[TR_FINANCE_BILLCHECK_PRICE]
	ON  [dbo].[FINANCE_BILLCHECK]
AFTER INSERT, UPDATE, DELETE
AS 
BEGIN
	SET NOCOUNT ON;

	IF (TRIGGER_NESTLEVEL() > 1)
		RETURN

	UPDATE T SET
		T.PRICE = BASE.PRICE,
		T.PRICE_TOTAL = TOTAL.PRICE,

		T.PRICE_INCVAT = BASE.PRICE_INCVAT,
		T.PRICE_TOTAL_INCVAT = TOTAL.PRICE_INCVAT,
		
		T.VAT_TOTAL = VAT.VAT
	FROM [FINANCE_BILLCHECK] T
	INNER JOIN INSERTED I ON T.ID = I.ID
	LEFT JOIN FINANCE_VAT FV WITH (NOLOCK) ON FV.ID = I.FK_FINANCE_VAT
	OUTER APPLY (
		SELECT
			ROUND(I.PRICE, 2) AS PRICE,
			ROUND(ROUND(I.PRICE, 2) * (1 + ISNULL((FV.[PERCENTAGE] / 100), 0)), 2) AS PRICE_INCVAT
	) BASE
	OUTER APPLY (
		SELECT
			ROUND(BASE.PRICE * ISNULL(I.QUANTITY, 0) * ISNULL(I.QUANTITY_MONTH, 1), 2) AS PRICE,
			ROUND(BASE.PRICE_INCVAT * ISNULL(I.QUANTITY, 0) * ISNULL(I.QUANTITY_MONTH, 1), 2) AS PRICE_INCVAT
	) TOTAL
	OUTER APPLY (
		SELECT
			ISNULL(TOTAL.PRICE_INCVAT, 0) - ISNULL(TOTAL.PRICE, 0) AS VAT
	) VAT

END
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO
CREATE TRIGGER [dbo].[TR_FINANCE_BILLCHECK_TS_LASTMODIFIED]
						   ON  [dbo].[FINANCE_BILLCHECK]
						   AFTER UPDATE
						AS 
						BEGIN
							SET NOCOUNT ON;

							UPDATE T SET 
								T.TS_LASTMODIFIED = GETDATE()
							FROM 
								FINANCE_BILLCHECK T
							INNER JOIN INSERTED I ON T.ID = I.ID
						END
GO
ALTER TABLE [dbo].[FINANCE_BILLCHECK] ADD CONSTRAINT [PK_FINANCE_BILLCHECK] PRIMARY KEY CLUSTERED  ([ID]) ON [PRIMARY]
GO
ALTER TABLE [dbo].[FINANCE_BILLCHECK] ADD CONSTRAINT [FK_FINANCE_BILLCHECK_CORE_LABEL] FOREIGN KEY ([FK_CORE_LABEL]) REFERENCES [dbo].[CORE_LABEL] ([ID])
GO
ALTER TABLE [dbo].[FINANCE_BILLCHECK] ADD CONSTRAINT [FK_FINANCE_BILLCHECK_CRM_CONTACT] FOREIGN KEY ([FK_CRM_CONTACT]) REFERENCES [dbo].[CRM_CONTACT] ([ID])
GO
ALTER TABLE [dbo].[FINANCE_BILLCHECK] ADD CONSTRAINT [FK_FINANCE_BILLCHECK_CRM_RELATION] FOREIGN KEY ([FK_CRM_RELATION]) REFERENCES [dbo].[CRM_RELATION] ([ID])
GO
ALTER TABLE [dbo].[FINANCE_BILLCHECK] ADD CONSTRAINT [FK_FINANCE_BILLCHECK_CRM_RELATION_ADDRESS] FOREIGN KEY ([FK_CRM_RELATION_ADDRESS]) REFERENCES [dbo].[CRM_RELATION_ADDRESS] ([ID])
GO
ALTER TABLE [dbo].[FINANCE_BILLCHECK] ADD CONSTRAINT [FK_FINANCE_BILLCHECK_FINANCE_INVOICE] FOREIGN KEY ([FK_FINANCE_INVOICE_TARGET]) REFERENCES [dbo].[FINANCE_INVOICE] ([ID])
GO
ALTER TABLE [dbo].[FINANCE_BILLCHECK] ADD CONSTRAINT [FK_FINANCE_BILLCHECK_FINANCE_INVOICE_COLLECT_INTERVAL] FOREIGN KEY ([FK_FINANCE_INVOICE_COLLECT_INTERVAL]) REFERENCES [dbo].[FINANCE_INVOICE_COLLECT_INTERVAL] ([ID])
GO
ALTER TABLE [dbo].[FINANCE_BILLCHECK] ADD CONSTRAINT [FK_FINANCE_BILLCHECK_FINANCE_INVOICE_SCHEME] FOREIGN KEY ([FK_FINANCE_INVOICE_SCHEME]) REFERENCES [dbo].[FINANCE_INVOICE_SCHEME] ([ID])
GO
ALTER TABLE [dbo].[FINANCE_BILLCHECK] ADD CONSTRAINT [FK_FINANCE_BILLCHECK_FINANCE_LEDGER] FOREIGN KEY ([FK_FINANCE_LEDGER]) REFERENCES [dbo].[FINANCE_LEDGER] ([ID])
GO
ALTER TABLE [dbo].[FINANCE_BILLCHECK] ADD CONSTRAINT [FK_FINANCE_BILLCHECK_FINANCE_VAT] FOREIGN KEY ([FK_FINANCE_VAT]) REFERENCES [dbo].[FINANCE_VAT] ([ID])
GO
ALTER TABLE [dbo].[FINANCE_BILLCHECK] ADD CONSTRAINT [FK_FINANCE_BILLCHECK_PROJECT] FOREIGN KEY ([FK_PROJECT]) REFERENCES [dbo].[PROJECT] ([ID])
GO
