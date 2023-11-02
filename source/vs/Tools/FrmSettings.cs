using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace Tools
{
	public partial class FrmSettings : Form
	{
		public FrmSettings()
		{
			InitializeComponent();
			TxtA.Value = (decimal)Tools.Properties.Settings.Default.margin_x;
			TxtB.Value = (decimal)Tools.Properties.Settings.Default.margin_y;
			TxtC.Value = (decimal)Tools.Properties.Settings.Default.int_x;
			TxtD.Value = (decimal)Tools.Properties.Settings.Default.int_y;
			TxtE.Value = (decimal)Tools.Properties.Settings.Default.waku_w;
			TxtF.Value = (decimal)Tools.Properties.Settings.Default.waku_h;
			TxtNumX.Value = (decimal)Tools.Properties.Settings.Default.num_x;
			TxtNumY.Value = (decimal)Tools.Properties.Settings.Default.num_y;
		}

		private void CmdClose_Click(object sender, EventArgs e)
		{
			this.Close();
		}

		private void CmdSave_Click(object sender, EventArgs e)
		{
			Tools.Properties.Settings.Default.margin_x = (float)TxtA.Value;
			Tools.Properties.Settings.Default.margin_y = (float)TxtB.Value;
			Tools.Properties.Settings.Default.int_x = (float)TxtC.Value;
			Tools.Properties.Settings.Default.int_y = (float)TxtD.Value;
			Tools.Properties.Settings.Default.waku_w = (float)TxtE.Value;
			Tools.Properties.Settings.Default.waku_h = (float)TxtF.Value;
			Tools.Properties.Settings.Default.num_x = (int)TxtNumX.Value;
			Tools.Properties.Settings.Default.num_y = (int)TxtNumY.Value;
			Tools.Properties.Settings.Default.Save();
			this.Close();
		}
	}
}
