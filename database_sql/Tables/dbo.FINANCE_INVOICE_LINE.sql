CREATE TABLE [dbo].[FINANCE_INVOICE_LINE]
(
[ID] [int] NOT NULL IDENTITY(1, 1),
[TS_CREATED] [datetime] NOT NULL CONSTRAINT [DF_FINANCE_INVOICE_LINE_TS_CREATED] DEFAULT (getdate()),
[TS_LASTMODIFIED] [datetime] NULL CONSTRAINT [DF_FINANCE_INVOICE_LINE_TS_LASTMODIFIED] DEFAULT (getdate()),
[ACTIVE] [bit] NOT NULL CONSTRAINT [DF_FINANCE_INVOICE_LINE_ACTIVE] DEFAULT ((1)),
[FK_FINANCE_INVOICE] [int] NULL,
[FK_PROJECT] [int] NULL,
[FK_FINANCE_LEDGER] [int] NULL,
[FK_FINANCE_VAT] [int] NULL,
[FK_ASSORTMENT_PRODUCT] [int] NULL,
[FK_FINANCE_INVOICE_SCHEME] [int] NULL,
[FK_FINANCE_INVOICE_SECTION] [int] NULL,
[QUANTITY] [decimal] (15, 6) NULL,
[QUANTITY_MONTH] [decimal] (15, 6) NULL,
[DESCRIPTION] [nvarchar] (4000) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[DESCRIPTION_SPECIFICATION] [nvarchar] (4000) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[SHOW_ON_SPECIFICATION] [bit] NULL CONSTRAINT [DF_FINANCE_INVOICE_LINE_SHOW_ON_SPECIFICATION] DEFAULT ((0)),
[PRICE] [decimal] (15, 6) NULL,
[PRICE_TOTAL] [decimal] (15, 6) NULL,
[PRICE_INCVAT] [decimal] (15, 6) NULL,
[PRICE_TOTAL_INCVAT] [decimal] (15, 6) NULL,
[VAT_TOTAL] [decimal] (15, 6) NULL,
[STARTDATE] [datetime] NULL,
[ENDDATE] [datetime] NULL
) ON [PRIMARY]
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO
CREATE TRIGGER [dbo].[TR_FINANCE_INVOICE_LINE_PRICE]
	ON  [dbo].[FINANCE_INVOICE_LINE]
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
	FROM FINANCE_INVOICE_LINE T
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


	/* UPDATE INVOICE TOTALS */
	DECLARE @FK_FINANCE_INVOICE INT

	DECLARE INVOICE_CURSOR CURSOR FOR
		SELECT FK_FINANCE_INVOICE
		FROM Inserted
		UNION
		SELECT FK_FINANCE_INVOICE
		FROM Deleted;

	OPEN INVOICE_CURSOR;

	FETCH NEXT FROM INVOICE_CURSOR
	INTO @FK_FINANCE_INVOICE

	WHILE @@FETCH_STATUS = 0
	BEGIN

		EXEC [FINANCE_INVOICE_RECALCULATE_TOTALS] @FK_FINANCE_INVOICE
		
		FETCH NEXT FROM INVOICE_CURSOR
		INTO @FK_FINANCE_INVOICE

	END

	CLOSE INVOICE_CURSOR;
	DEALLOCATE INVOICE_CURSOR;

END
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO
CREATE TRIGGER [dbo].[TR_FINANCE_INVOICE_LINE_TS_LASTMODIFIED]
						   ON  [dbo].[FINANCE_INVOICE_LINE]
						   AFTER UPDATE
						AS 
						BEGIN
							SET NOCOUNT ON;

							UPDATE T SET 
								T.TS_LASTMODIFIED = GETDATE()
							FROM 
								FINANCE_INVOICE_LINE T
							INNER JOIN INSERTED I ON T.ID = I.ID
						END
GO
ALTER TABLE [dbo].[FINANCE_INVOICE_LINE] ADD CONSTRAINT [PK_FINANCE_INVOICE_LINE] PRIMARY KEY CLUSTERED  ([ID]) ON [PRIMARY]
GO
ALTER TABLE [dbo].[FINANCE_INVOICE_LINE] ADD CONSTRAINT [FK_FINANCE_INVOICE_LINE_ASSORTMENT_PRODUCT] FOREIGN KEY ([FK_ASSORTMENT_PRODUCT]) REFERENCES [dbo].[ASSORTMENT_PRODUCT] ([ID])
GO
ALTER TABLE [dbo].[FINANCE_INVOICE_LINE] ADD CONSTRAINT [FK_FINANCE_INVOICE_LINE_FINANCE_INVOICE] FOREIGN KEY ([FK_FINANCE_INVOICE]) REFERENCES [dbo].[FINANCE_INVOICE] ([ID])
GO
ALTER TABLE [dbo].[FINANCE_INVOICE_LINE] ADD CONSTRAINT [FK_FINANCE_INVOICE_LINE_FINANCE_INVOICE_SCHEME] FOREIGN KEY ([FK_FINANCE_INVOICE_SCHEME]) REFERENCES [dbo].[FINANCE_INVOICE_SCHEME] ([ID])
GO
ALTER TABLE [dbo].[FINANCE_INVOICE_LINE] ADD CONSTRAINT [FK_FINANCE_INVOICE_LINE_FINANCE_INVOICE_SECTION] FOREIGN KEY ([FK_FINANCE_INVOICE_SECTION]) REFERENCES [dbo].[FINANCE_INVOICE_SECTION] ([ID])
GO
ALTER TABLE [dbo].[FINANCE_INVOICE_LINE] ADD CONSTRAINT [FK_FINANCE_INVOICE_LINE_FINANCE_LEDGER] FOREIGN KEY ([FK_FINANCE_LEDGER]) REFERENCES [dbo].[FINANCE_LEDGER] ([ID])
GO
ALTER TABLE [dbo].[FINANCE_INVOICE_LINE] ADD CONSTRAINT [FK_FINANCE_INVOICE_LINE_FINANCE_VAT] FOREIGN KEY ([FK_FINANCE_VAT]) REFERENCES [dbo].[FINANCE_VAT] ([ID])
GO
ALTER TABLE [dbo].[FINANCE_INVOICE_LINE] ADD CONSTRAINT [FK_FINANCE_INVOICE_LINE_PROJECT] FOREIGN KEY ([FK_PROJECT]) REFERENCES [dbo].[PROJECT] ([ID])
GO
