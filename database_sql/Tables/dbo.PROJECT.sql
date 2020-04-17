CREATE TABLE [dbo].[PROJECT]
(
[ID] [int] NOT NULL IDENTITY(1, 1),
[TS_CREATED] [datetime] NOT NULL CONSTRAINT [DF_PROJECT_TS_CREATED] DEFAULT (getdate()),
[TS_LASTMODIFIED] [datetime] NULL CONSTRAINT [DF_PROJECT_TS_LASTMODIFIED] DEFAULT (getdate()),
[TS_LASTMODIFIED_STATE] [datetime] NULL CONSTRAINT [DF_PROJECT_TS_LASTMODIFIED_STATE] DEFAULT (getdate()),
[ACTIVE] [bit] NOT NULL CONSTRAINT [DF_PROJECT_ACTIVE] DEFAULT ((1)),
[FK_CORE_LABEL] [int] NULL,
[FK_CRM_RELATION_REFERRER] [int] NULL,
[FK_CRM_CONTACT_REFERRER] [int] NULL,
[FK_CRM_RELATION_EMPLOYER] [int] NULL,
[FK_CRM_CONTACT_EMPLOYER] [int] NULL,
[FK_CRM_CONTACT_EMPLOYEE] [int] NULL,
[FK_CORE_DROPDOWNVALUE_PROJECTTYPE] [int] NULL,
[FK_CORE_WORKFLOWSTATE] [int] NULL,
[CREATE_FK_CORE_USER] [int] NULL,
[FK_FINANCE_INVOICE_LINE] [int] NULL,
[INVOICING_COMPLETE] [bit] NULL CONSTRAINT [DF_PROJECT_INVOICE_COMPLETE] DEFAULT ((0)),
[COMPENSATION_PERCENTAGE] [decimal] (15, 6) NULL,
[COMPENSATION_PRICE] [decimal] (15, 6) NULL,
[START_DATE] [datetime] NULL,
[POLICY_NUMBER] [nvarchar] (255) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[DESCRIPTION] [nvarchar] (255) COLLATE SQL_Latin1_General_CP1_CI_AS NULL
) ON [PRIMARY]
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO
CREATE TRIGGER [dbo].[TR_PROJECT_TS_LASTMODIFIED]
	ON  [dbo].[PROJECT]
	AFTER UPDATE
AS 
BEGIN
	SET NOCOUNT ON;

	UPDATE T SET 
		T.TS_LASTMODIFIED = GETDATE()
	FROM 
		PROJECT T
	INNER JOIN INSERTED I ON T.ID = I.ID
END
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO
CREATE TRIGGER [dbo].[TR_PROJECT_TS_LASTMODIFIED_STATE]
	ON  [dbo].[PROJECT]
	AFTER UPDATE
AS 
BEGIN
	SET NOCOUNT ON;

	IF UPDATE(FK_CORE_WORKFLOWSTATE)
	BEGIN
		UPDATE T SET 
			T.TS_LASTMODIFIED_STATE = GETDATE()
		FROM 
			PROJECT T
		INNER JOIN INSERTED I ON T.ID = I.ID
	END
END
GO
ALTER TABLE [dbo].[PROJECT] ADD CONSTRAINT [PK_PROJECT] PRIMARY KEY CLUSTERED  ([ID]) ON [PRIMARY]
GO
ALTER TABLE [dbo].[PROJECT] ADD CONSTRAINT [FK_PROJECT_CORE_DROPDOWNVALUE] FOREIGN KEY ([FK_CORE_DROPDOWNVALUE_PROJECTTYPE]) REFERENCES [dbo].[CORE_DROPDOWNVALUE] ([ID])
GO
ALTER TABLE [dbo].[PROJECT] ADD CONSTRAINT [FK_PROJECT_CORE_LABEL] FOREIGN KEY ([FK_CORE_LABEL]) REFERENCES [dbo].[CORE_LABEL] ([ID])
GO
ALTER TABLE [dbo].[PROJECT] ADD CONSTRAINT [FK_PROJECT_CORE_USER] FOREIGN KEY ([CREATE_FK_CORE_USER]) REFERENCES [dbo].[CORE_USER] ([ID])
GO
ALTER TABLE [dbo].[PROJECT] ADD CONSTRAINT [FK_PROJECT_CORE_WORKFLOWSTATE] FOREIGN KEY ([FK_CORE_WORKFLOWSTATE]) REFERENCES [dbo].[CORE_WORKFLOWSTATE] ([ID])
GO
ALTER TABLE [dbo].[PROJECT] ADD CONSTRAINT [FK_PROJECT_CRM_CONTACT_EMPLOYEE] FOREIGN KEY ([FK_CRM_CONTACT_EMPLOYEE]) REFERENCES [dbo].[CRM_CONTACT] ([ID])
GO
ALTER TABLE [dbo].[PROJECT] ADD CONSTRAINT [FK_PROJECT_CRM_CONTACT_EMPLOYER] FOREIGN KEY ([FK_CRM_CONTACT_EMPLOYER]) REFERENCES [dbo].[CRM_CONTACT] ([ID])
GO
ALTER TABLE [dbo].[PROJECT] ADD CONSTRAINT [FK_PROJECT_CRM_CONTACT_REFERRER] FOREIGN KEY ([FK_CRM_CONTACT_REFERRER]) REFERENCES [dbo].[CRM_CONTACT] ([ID])
GO
ALTER TABLE [dbo].[PROJECT] ADD CONSTRAINT [FK_PROJECT_CRM_RELATION_EMPLOYER] FOREIGN KEY ([FK_CRM_RELATION_EMPLOYER]) REFERENCES [dbo].[CRM_RELATION] ([ID])
GO
ALTER TABLE [dbo].[PROJECT] ADD CONSTRAINT [FK_PROJECT_CRM_RELATION_REFERRER] FOREIGN KEY ([FK_CRM_RELATION_REFERRER]) REFERENCES [dbo].[CRM_RELATION] ([ID])
GO
ALTER TABLE [dbo].[PROJECT] ADD CONSTRAINT [FK_PROJECT_FINANCE_INVOICE_LINE] FOREIGN KEY ([FK_FINANCE_INVOICE_LINE]) REFERENCES [dbo].[FINANCE_INVOICE_LINE] ([ID])
GO
