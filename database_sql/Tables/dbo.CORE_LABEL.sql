CREATE TABLE [dbo].[CORE_LABEL]
(
[ID] [int] NOT NULL IDENTITY(1, 1),
[ACTIVE] [bit] NOT NULL CONSTRAINT [DF_CORE_LABELS_ACTIVE] DEFAULT ((1)),
[SEQUENCE] [float] NULL,
[TS_CREATED] [datetime] NOT NULL CONSTRAINT [DF_CORE_LABELS_TS_CREATED] DEFAULT (getdate()),
[TS_LASTMODIFIED] [datetime] NULL CONSTRAINT [DF_CORE_LABELS_TS_LASTMODIFIED] DEFAULT (getdate()),
[DESCRIPTION] [nvarchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[LOGO_EMAIL] [nvarchar] (500) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[EMAIL_SENDER_NAME] [nvarchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[EMAIL_SENDER_EMAIL] [nvarchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[NEXT_INVOICE_NUMBER] [int] NULL,
[NEXT_DEBTOR_NUMBER] [int] NULL,
[NEXT_CREDITOR_NUMBER] [int] NULL,
[DEFAULT_DIGITAL_INVOICE] [bit] NULL,
[DEFAULT_PAYMENTTERM_DAY] [int] NULL,
[DEFAULT_RATE_KM] [decimal] (30, 15) NULL,
[FK_FINANCE_LEDGER_DEFAULT_COMPENSATION] [int] NULL,
[FK_FINANCE_VAT_DEFAULT_COMPENSATION] [int] NULL,
[FK_FINANCE_VAT_SHIFTED] [int] NULL,
[FK_DOCUMENT_PDF_PAPER] [int] NULL,
[IBAN_NUMBER] [nvarchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[BIC_NUMBER] [nvarchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[VAT_NUMBER] [nvarchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL
) ON [PRIMARY]
GO
ALTER TABLE [dbo].[CORE_LABEL] ADD CONSTRAINT [PK_CORE_LABELS] PRIMARY KEY CLUSTERED  ([ID]) ON [PRIMARY]
GO
ALTER TABLE [dbo].[CORE_LABEL] ADD CONSTRAINT [FK_CORE_LABEL_DOCUMENT] FOREIGN KEY ([FK_DOCUMENT_PDF_PAPER]) REFERENCES [dbo].[DOCUMENT] ([ID])
GO
ALTER TABLE [dbo].[CORE_LABEL] ADD CONSTRAINT [FK_CORE_LABEL_FINANCE_LEDGER] FOREIGN KEY ([FK_FINANCE_LEDGER_DEFAULT_COMPENSATION]) REFERENCES [dbo].[FINANCE_LEDGER] ([ID])
GO
ALTER TABLE [dbo].[CORE_LABEL] ADD CONSTRAINT [FK_CORE_LABEL_FINANCE_VAT] FOREIGN KEY ([FK_FINANCE_VAT_DEFAULT_COMPENSATION]) REFERENCES [dbo].[FINANCE_VAT] ([ID])
GO
ALTER TABLE [dbo].[CORE_LABEL] ADD CONSTRAINT [FK_CORE_LABEL_FINANCE_VAT1] FOREIGN KEY ([FK_FINANCE_VAT_SHIFTED]) REFERENCES [dbo].[FINANCE_VAT] ([ID])
GO
