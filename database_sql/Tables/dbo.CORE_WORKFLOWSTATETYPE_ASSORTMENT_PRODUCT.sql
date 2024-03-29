CREATE TABLE [dbo].[CORE_WORKFLOWSTATETYPE_ASSORTMENT_PRODUCT]
(
[ID] [int] NOT NULL IDENTITY(1, 1),
[TS_CREATED] [datetime] NOT NULL CONSTRAINT [DF_CORE_WORKFLOWSTATETYPE_ASSORTMENT_PRODUCT_TS_CREATED] DEFAULT (getdate()),
[TS_LASTMODIFIED] [datetime] NULL CONSTRAINT [DF_CORE_WORKFLOWSTATETYPE_ASSORTMENT_PRODUCT_TS_LASTMODIFIED] DEFAULT (getdate()),
[ACTIVE] [bit] NOT NULL CONSTRAINT [DF_CORE_WORKFLOWSTATETYPE_ASSORTMENT_PRODUCT_ACTIVE] DEFAULT ((1)),
[FK_CORE_WORKFLOWSTATETYPE] [int] NULL,
[FK_ASSORTMENT_PRODUCT] [int] NULL
) ON [PRIMARY]
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO

				CREATE TRIGGER [dbo].[TR_CORE_WORKFLOWSTATETYPE_ASSORTMENT_PRODUCT_TS_LASTMODIFIED]
						   ON  [dbo].[CORE_WORKFLOWSTATETYPE_ASSORTMENT_PRODUCT]
						   AFTER UPDATE
						AS 
						BEGIN
							SET NOCOUNT ON;

							UPDATE T SET 
								T.TS_LASTMODIFIED = GETDATE()
							FROM 
								CORE_WORKFLOWSTATETYPE_ASSORTMENT_PRODUCT T
							INNER JOIN INSERTED I ON T.ID = I.ID
						END
GO
ALTER TABLE [dbo].[CORE_WORKFLOWSTATETYPE_ASSORTMENT_PRODUCT] ADD CONSTRAINT [PK_CORE_WORKFLOWSTATETYPE_ASSORTMENT_PRODUCT] PRIMARY KEY CLUSTERED  ([ID]) ON [PRIMARY]
GO
ALTER TABLE [dbo].[CORE_WORKFLOWSTATETYPE_ASSORTMENT_PRODUCT] ADD CONSTRAINT [FK_CORE_WORKFLOWSTATETYPE_ASSORTMENT_PRODUCT_ASSORTMENT_PRODUCT] FOREIGN KEY ([FK_ASSORTMENT_PRODUCT]) REFERENCES [dbo].[ASSORTMENT_PRODUCT] ([ID])
GO
ALTER TABLE [dbo].[CORE_WORKFLOWSTATETYPE_ASSORTMENT_PRODUCT] ADD CONSTRAINT [FK_CORE_WORKFLOWSTATETYPE_ASSORTMENT_PRODUCT_CORE_WORKFLOWSTATETYPE] FOREIGN KEY ([FK_CORE_WORKFLOWSTATETYPE]) REFERENCES [dbo].[CORE_WORKFLOWSTATETYPE] ([ID])
GO
