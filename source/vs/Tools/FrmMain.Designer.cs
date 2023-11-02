namespace Tools
{
	partial class FrmMain
	{
		/// <summary>
		/// 必要なデザイナー変数です。
		/// </summary>
		private System.ComponentModel.IContainer components = null;

		/// <summary>
		/// 使用中のリソースをすべてクリーンアップします。
		/// </summary>
		/// <param name="disposing">マネージド リソースを破棄する場合は true を指定し、その他の場合は false を指定します。</param>
		protected override void Dispose(bool disposing)
		{
			if (disposing && (components != null))
			{
				components.Dispose();
			}
			base.Dispose(disposing);
		}

		#region Windows フォーム デザイナーで生成されたコード

		/// <summary>
		/// デザイナー サポートに必要なメソッドです。このメソッドの内容を
		/// コード エディターで変更しないでください。
		/// </summary>
		private void InitializeComponent()
		{
			System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(FrmMain));
			this.CmdPrint = new System.Windows.Forms.Button();
			this.CmdOpen = new System.Windows.Forms.Button();
			this.ListMain = new System.Windows.Forms.ListView();
			this.panel1 = new System.Windows.Forms.Panel();
			this.CmdSettings = new System.Windows.Forms.Button();
			this.CmdClear = new System.Windows.Forms.Button();
			this.label2 = new System.Windows.Forms.Label();
			this.label1 = new System.Windows.Forms.Label();
			this.TxtX = new System.Windows.Forms.NumericUpDown();
			this.TxtY = new System.Windows.Forms.NumericUpDown();
			this.CmdAllSel = new System.Windows.Forms.Button();
			this.CmdAllAnsel = new System.Windows.Forms.Button();
			this.panel1.SuspendLayout();
			((System.ComponentModel.ISupportInitialize)(this.TxtX)).BeginInit();
			((System.ComponentModel.ISupportInitialize)(this.TxtY)).BeginInit();
			this.SuspendLayout();
			// 
			// CmdPrint
			// 
			this.CmdPrint.Location = new System.Drawing.Point(174, 12);
			this.CmdPrint.Name = "CmdPrint";
			this.CmdPrint.Size = new System.Drawing.Size(75, 23);
			this.CmdPrint.TabIndex = 1;
			this.CmdPrint.Text = "印刷";
			this.CmdPrint.UseVisualStyleBackColor = true;
			this.CmdPrint.Click += new System.EventHandler(this.CmdPrint_Click);
			// 
			// CmdOpen
			// 
			this.CmdOpen.Location = new System.Drawing.Point(12, 12);
			this.CmdOpen.Name = "CmdOpen";
			this.CmdOpen.Size = new System.Drawing.Size(75, 23);
			this.CmdOpen.TabIndex = 2;
			this.CmdOpen.Text = "開く";
			this.CmdOpen.UseVisualStyleBackColor = true;
			this.CmdOpen.Click += new System.EventHandler(this.CmdOpen_Click);
			// 
			// ListMain
			// 
			this.ListMain.Dock = System.Windows.Forms.DockStyle.Fill;
			this.ListMain.FullRowSelect = true;
			this.ListMain.GridLines = true;
			this.ListMain.HideSelection = false;
			this.ListMain.Location = new System.Drawing.Point(0, 45);
			this.ListMain.Name = "ListMain";
			this.ListMain.Size = new System.Drawing.Size(1036, 566);
			this.ListMain.TabIndex = 3;
			this.ListMain.UseCompatibleStateImageBehavior = false;
			this.ListMain.View = System.Windows.Forms.View.Details;
			// 
			// panel1
			// 
			this.panel1.Controls.Add(this.CmdAllAnsel);
			this.panel1.Controls.Add(this.CmdAllSel);
			this.panel1.Controls.Add(this.CmdSettings);
			this.panel1.Controls.Add(this.CmdClear);
			this.panel1.Controls.Add(this.label2);
			this.panel1.Controls.Add(this.label1);
			this.panel1.Controls.Add(this.TxtX);
			this.panel1.Controls.Add(this.TxtY);
			this.panel1.Controls.Add(this.CmdOpen);
			this.panel1.Controls.Add(this.CmdPrint);
			this.panel1.Dock = System.Windows.Forms.DockStyle.Top;
			this.panel1.Location = new System.Drawing.Point(0, 0);
			this.panel1.Name = "panel1";
			this.panel1.Size = new System.Drawing.Size(1036, 45);
			this.panel1.TabIndex = 4;
			// 
			// CmdSettings
			// 
			this.CmdSettings.Location = new System.Drawing.Point(543, 11);
			this.CmdSettings.Name = "CmdSettings";
			this.CmdSettings.Size = new System.Drawing.Size(75, 23);
			this.CmdSettings.TabIndex = 8;
			this.CmdSettings.Text = "設定";
			this.CmdSettings.UseVisualStyleBackColor = true;
			this.CmdSettings.Click += new System.EventHandler(this.CmdSettings_Click);
			// 
			// CmdClear
			// 
			this.CmdClear.Location = new System.Drawing.Point(93, 12);
			this.CmdClear.Name = "CmdClear";
			this.CmdClear.Size = new System.Drawing.Size(75, 23);
			this.CmdClear.TabIndex = 7;
			this.CmdClear.Text = "クリア";
			this.CmdClear.UseVisualStyleBackColor = true;
			this.CmdClear.Click += new System.EventHandler(this.CmdClear_Click);
			// 
			// label2
			// 
			this.label2.AutoSize = true;
			this.label2.Location = new System.Drawing.Point(438, 17);
			this.label2.Name = "label2";
			this.label2.Size = new System.Drawing.Size(17, 12);
			this.label2.TabIndex = 6;
			this.label2.Text = "列";
			// 
			// label1
			// 
			this.label1.AutoSize = true;
			this.label1.Location = new System.Drawing.Point(255, 16);
			this.label1.Name = "label1";
			this.label1.Size = new System.Drawing.Size(95, 12);
			this.label1.TabIndex = 5;
			this.label1.Text = "印刷開始位置-行";
			// 
			// TxtX
			// 
			this.TxtX.Location = new System.Drawing.Point(461, 14);
			this.TxtX.Name = "TxtX";
			this.TxtX.Size = new System.Drawing.Size(76, 19);
			this.TxtX.TabIndex = 4;
			this.TxtX.Value = new decimal(new int[] {
            1,
            0,
            0,
            0});
			// 
			// TxtY
			// 
			this.TxtY.Location = new System.Drawing.Point(356, 14);
			this.TxtY.Name = "TxtY";
			this.TxtY.Size = new System.Drawing.Size(76, 19);
			this.TxtY.TabIndex = 3;
			this.TxtY.Value = new decimal(new int[] {
            1,
            0,
            0,
            0});
			// 
			// CmdAllSel
			// 
			this.CmdAllSel.Location = new System.Drawing.Point(624, 12);
			this.CmdAllSel.Name = "CmdAllSel";
			this.CmdAllSel.Size = new System.Drawing.Size(75, 23);
			this.CmdAllSel.TabIndex = 9;
			this.CmdAllSel.Text = "全選択";
			this.CmdAllSel.UseVisualStyleBackColor = true;
			this.CmdAllSel.Click += new System.EventHandler(this.CmdAllSel_Click);
			// 
			// CmdAllAnsel
			// 
			this.CmdAllAnsel.Location = new System.Drawing.Point(705, 12);
			this.CmdAllAnsel.Name = "CmdAllAnsel";
			this.CmdAllAnsel.Size = new System.Drawing.Size(75, 23);
			this.CmdAllAnsel.TabIndex = 10;
			this.CmdAllAnsel.Text = "全解除";
			this.CmdAllAnsel.UseVisualStyleBackColor = true;
			this.CmdAllAnsel.Click += new System.EventHandler(this.CmdAllAnsel_Click);
			// 
			// FrmMain
			// 
			this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.None;
			this.ClientSize = new System.Drawing.Size(1036, 611);
			this.Controls.Add(this.ListMain);
			this.Controls.Add(this.panel1);
			this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
			this.MaximizeBox = false;
			this.MinimizeBox = false;
			this.Name = "FrmMain";
			this.Text = "部品在庫管理ＱＲコード印刷";
			this.FormClosed += new System.Windows.Forms.FormClosedEventHandler(this.FrmMain_FormClosed);
			this.panel1.ResumeLayout(false);
			this.panel1.PerformLayout();
			((System.ComponentModel.ISupportInitialize)(this.TxtX)).EndInit();
			((System.ComponentModel.ISupportInitialize)(this.TxtY)).EndInit();
			this.ResumeLayout(false);

		}

		#endregion
		private System.Windows.Forms.Button CmdPrint;
		private System.Windows.Forms.Button CmdOpen;
		private System.Windows.Forms.ListView ListMain;
		private System.Windows.Forms.Panel panel1;
		private System.Windows.Forms.NumericUpDown TxtX;
		private System.Windows.Forms.NumericUpDown TxtY;
		private System.Windows.Forms.Label label2;
		private System.Windows.Forms.Label label1;
		private System.Windows.Forms.Button CmdClear;
		private System.Windows.Forms.Button CmdSettings;
		private System.Windows.Forms.Button CmdAllAnsel;
		private System.Windows.Forms.Button CmdAllSel;
	}
}

