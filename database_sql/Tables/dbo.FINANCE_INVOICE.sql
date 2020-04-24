CREATE TABLE [dbo].[FINANCE_INVOICE]
(
[ID] [int] NOT NULL IDENTITY(1, 1),
[TS_CREATED] [datetime] NOT NULL CONSTRAINT [DF_FINANCE_INVOICE_TS_CREATED] DEFAULT (getdate()),
[TS_LASTMODIFIED] [datetime] NULL CONSTRAINT [DF_FINANCE_INVOICE_TS_LASTMODIFIED] DEFAULT (getdate()),
[ACTIVE] [bit] NOT NULL CONSTRAINT [DF_FINANCE_INVOICE_ACTIVE] DEFAULT ((1)),
[FK_CRM_RELATION] [int] NOT NULL,
[FK_CRM_CONTACT] [int] NOT NULL,
[FK_CORE_WORKFLOWSTATE] [int] NULL,
[FK_FINANCE_INVOICE_CREDIT] [int] NULL,
[FK_FINANCE_INVOICE_ORIGINAL] [int] NULL,
[FK_CORE_LABEL] [int] NOT NULL,
[FK_PROJECT] [int] NULL,
[FK_DOCUMENT] [int] NULL,
[FK_CRM_RELATION_ADDRESS] [int] NULL,
[MANUAL] [bit] NULL CONSTRAINT [DF_FINANCE_INVOICE_MANUAL] DEFAULT ((0)),
[NUMBER] [int] NULL,
[DATE] [date] NULL,
[EXPIRATION_DATE] [date] NULL,
[REMARKS] [nvarchar] (max) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[PAID] [bit] NULL CONSTRAINT [DF_FINANCE_INVOICE_PAID] DEFAULT ((0)),
[PAID_DATE] [date] NULL,
[PRICE_TOTAL_EXCL] [decimal] (15, 6) NULL,
[VAT_TOTAL] [decimal] (15, 6) NULL,
[PRICE_TOTAL_INCL] [decimal] (15, 6) NULL,
[IS_ADVANCE] [bit] NULL CONSTRAINT [DF_FINANCE_INVOICE_IS_ADVANCE] DEFAULT ((0)),
[IS_COLLECTIVE] [bit] NULL CONSTRAINT [DF_FINANCE_INVOICE_IS_COLLECTIVE] DEFAULT ((0)),
[IS_CREDIT] [bit] NULL CONSTRAINT [DF_FINANCE_INVOICE_IS_CREDIT] DEFAULT ((0)),
[IS_CREDITTED] [bit] NULL CONSTRAINT [DF_FINANCE_INVOICE_IS_CREDITTED] DEFAULT ((0)),
[TS_GENERATE] [datetime] NULL,
[HAS_SPECIFICATION] [bit] NULL CONSTRAINT [DF_FINANCE_INVOICE_HAS_SPECIFICATION] DEFAULT ((0)),
[EXACT_INVOICE_ID] [nvarchar] (255) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[EXACT_INVOICE_LASTSYNC] [datetime] NULL,
[EXACT_INVOICE_ERROR] [nvarchar] (max) COLLATE SQL_Latin1_General_CP1_CI_AS NULL
) ON [PRIMARY]
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO
CREATE TRIGGER [dbo].[TR_FINANCE_INVOICE_PAID_DATE]
ON  [dbo].[FINANCE_INVOICE]
AFTER UPDATE
AS 
BEGIN
	SET NOCOUNT ON;

	IF UPDATE (PAID)
    BEGIN
		UPDATE T SET 
			T.PAID_DATE = GETDATE()
		FROM 
			FINANCE_INVOICE  T
		INNER JOIN INSERTED I ON T.ID = I.ID
	END

END
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO
CREATE TRIGGER [dbo].[TR_FINANCE_INVOICE_PROJECT_INVOICING_COMPLETE]
	ON  [dbo].[FINANCE_INVOICE]
AFTER INSERT, UPDATE, DELETE
AS 
BEGIN
	SET NOCOUNT ON;

	IF (TRIGGER_NESTLEVEL() > 1)
		RETURN

	/* SET INVOICING COMPLETE ON PROJECTS */
	DECLARE @FK_PROJECT INT

	DECLARE PROJECT_CURSOR CURSOR FOR
		SELECT FK_PROJECT
		FROM Inserted

		UNION
		
		SELECT FK_PROJECT
		FROM Deleted

		UNION

		SELECT
			FIL.FK_PROJECT
		FROM FINANCE_INVOICE_LINE FIL WITH (NOLOCK)
		WHERE FIL.FK_FINANCE_INVOICE IN (SELECT ID FROM Inserted)
		  AND FIL.ACTIVE = 1

		UNION

		SELECT
			FIL.FK_PROJECT
		FROM FINANCE_INVOICE_LINE FIL WITH (NOLOCK)
		WHERE FIL.FK_FINANCE_INVOICE IN (SELECT ID FROM Deleted)
		  AND FIL.ACTIVE = 1
	;

	OPEN PROJECT_CURSOR;

	FETCH NEXT FROM PROJECT_CURSOR
	INTO @FK_PROJECT

	WHILE @@FETCH_STATUS = 0
	BEGIN

		EXEC [PROJECT_CALCULATE_INVOICING_COMPLETE] @FK_PROJECT
		
		FETCH NEXT FROM PROJECT_CURSOR
		INTO @FK_PROJECT

	END

	CLOSE PROJECT_CURSOR;
	DEALLOCATE PROJECT_CURSOR;

END
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO
CREATE TRIGGER [dbo].[TR_FINANCE_INVOICE_TS_LASTMODIFIED]
						   ON  [dbo].[FINANCE_INVOICE]
						   AFTER UPDATE
						AS 
						BEGIN
							SET NOCOUNT ON;

							IF (
								NOT UPDATE(EXACT_INVOICE_ID) AND 
								NOT UPDATE(EXACT_INVOICE_LASTSYNC) AND 
								NOT UPDATE(EXACT_INVOICE_ERROR) AND
								NOT UPDATE(PAID) AND
								NOT UPDATE(PAID_DATE)
							)
							BEGIN

								UPDATE T SET 
									T.TS_LASTMODIFIED = GETDATE()
								FROM 
									FINANCE_INVOICE T
								INNER JOIN INSERTED I ON T.ID = I.ID

							END
						END
GO
ALTER TABLE [dbo].[FINANCE_INVOICE] ADD CONSTRAINT [PK_FINANCE_INVOICE] PRIMARY KEY CLUSTERED  ([ID]) ON [PRIMARY]
GO
ALTER TABLE [dbo].[FINANCE_INVOICE] ADD CONSTRAINT [FK_FINANCE_INVOICE_CORE_LABEL] FOREIGN KEY ([FK_CORE_LABEL]) REFERENCES [dbo].[CORE_LABEL] ([ID])
GO
ALTER TABLE [dbo].[FINANCE_INVOICE] ADD CONSTRAINT [FK_FINANCE_INVOICE_CORE_WORKFLOWSTATE] FOREIGN KEY ([FK_CORE_WORKFLOWSTATE]) REFERENCES [dbo].[CORE_WORKFLOWSTATE] ([ID])
GO
ALTER TABLE [dbo].[FINANCE_INVOICE] ADD CONSTRAINT [FK_FINANCE_INVOICE_CRM_CONTACT] FOREIGN KEY ([FK_CRM_CONTACT]) REFERENCES [dbo].[CRM_CONTACT] ([ID])
GO
ALTER TABLE [dbo].[FINANCE_INVOICE] ADD CONSTRAINT [FK_FINANCE_INVOICE_CRM_RELATION] FOREIGN KEY ([FK_CRM_RELATION]) REFERENCES [dbo].[CRM_RELATION] ([ID])
GO
ALTER TABLE [dbo].[FINANCE_INVOICE] ADD CONSTRAINT [FK_FINANCE_INVOICE_CRM_RELATION_ADDRESS] FOREIGN KEY ([FK_CRM_RELATION_ADDRESS]) REFERENCES [dbo].[CRM_RELATION_ADDRESS] ([ID])
GO
ALTER TABLE [dbo].[FINANCE_INVOICE] ADD CONSTRAINT [FK_FINANCE_INVOICE_DOCUMENT] FOREIGN KEY ([FK_DOCUMENT]) REFERENCES [dbo].[DOCUMENT] ([ID])
GO
ALTER TABLE [dbo].[FINANCE_INVOICE] ADD CONSTRAINT [FK_FINANCE_INVOICE_FINANCE_INVOICE] FOREIGN KEY ([FK_FINANCE_INVOICE_CREDIT]) REFERENCES [dbo].[FINANCE_INVOICE] ([ID])
GO
ALTER TABLE [dbo].[FINANCE_INVOICE] ADD CONSTRAINT [FK_FINANCE_INVOICE_FINANCE_INVOICE1] FOREIGN KEY ([FK_FINANCE_INVOICE_ORIGINAL]) REFERENCES [dbo].[FINANCE_INVOICE] ([ID])
GO
ALTER TABLE [dbo].[FINANCE_INVOICE] ADD CONSTRAINT [FK_FINANCE_INVOICE_PROJECT] FOREIGN KEY ([FK_PROJECT]) REFERENCES [dbo].[PROJECT] ([ID])
GO
