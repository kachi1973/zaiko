using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.Linq;
using System.Text.RegularExpressions;

namespace ClosedXML.Excel.CalcEngine
{
    internal class CalcEngineHelpers
    {
        private static Lazy<Dictionary<string, Tuple<string, string>>> patternReplacements =
            new Lazy<Dictionary<string, Tuple<string, string>>>(() =>
            {
                var patternReplacements = new Dictionary<string, Tuple<string, string>>();
                // key: the literal string to match
                // value: a tuple: first item: the search pattern, second item: the replacement
                patternReplacements.Add(@"~~", new Tuple<string, string>(@"~~", "~"));
                patternReplacements.Add(@"~*", new Tuple<string, string>(@"~\*", @"\*"));
                patternReplacements.Add(@"~?", new Tuple<string, string>(@"~\?", @"\?"));
                patternReplacements.Add(@"?", new Tuple<string, string>(@"\?", ".?"));
                patternReplacements.Add(@"*", new Tuple<string, string>(@"\*", ".*"));

                return patternReplacements;
            });

        internal static bool ValueSatisfiesCriteria(object value, object criteria, CalcEngine ce)
        {
            // safety...
            if (value == null)
            {
                return false;
            }

            // if criteria is a number, straight comparison
            if (criteria is double)
            {
                if (value is Double)
                    return (double)value == (double)criteria;
                return Double.TryParse(value.ToString(), out double dValue) && dValue == (double)criteria;
            }

            // convert criteria to string
            var cs = criteria as string;
            if (cs != null)
            {
                if (value is string && (value as string).Trim() == "")
                    return cs == "";

                if (cs == "")
                    return cs.Equals(value);

                // if criteria is an expression (e.g. ">20"), use calc engine
                if ((cs[0] == '=' && cs.IndexOfAny(new[] { '*', '?' }) < 0)
                    || cs[0] == '<'
                    || cs[0] == '>')
                {
                    // build expression
                    var expression = string.Format("{0}{1}", value, cs);

                    // add quotes if necessary
                    var pattern = @"([\w\s]+)(\W+)(\w+)";
                    var m = Regex.Match(expression, pattern);
                    if (m.Groups.Count == 4
                        && (!double.TryParse(m.Groups[1].Value, out double d) ||
                            !double.TryParse(m.Groups[3].Value, out d)))
                    {
                        expression = string.Format("\"{0}\"{1}\"{2}\"",
                                                   m.Groups[1].Value,
                                                   m.Groups[2].Value,
                                                   m.Groups[3].Value);
                    }

                    // evaluate
                    return (bool)ce.Evaluate(expression);
                }

                // if criteria is a regular expression, use regex
                if (cs.IndexOfAny(new[] { '*', '?' }) > -1)
                {
                    if (cs[0] == '=') cs = cs.Substring(1);

                    var pattern = Regex.Replace(
                        cs,
                        "(" + String.Join(
                                "|",
                                patternReplacements.Value.Values.Select(t => t.Item1))
                        + ")",
                        m => patternReplacements.Value[m.Value].Item2);
                    pattern = $"^{pattern}$";

                    return Regex.IsMatch(value.ToString(), pattern, RegexOptions.IgnoreCase);
                }

                // straight string comparison
                return string.Equals(value.ToString(), cs, StringComparison.OrdinalIgnoreCase);
            }

            // should never get here?
            Debug.Assert(false, "failed to evaluate criteria in SumIf");
            return false;
        }

        internal static bool ValueIsBlank(object value)
        {
            return
                value == null ||
                value is string && ((string)value).Length == 0;
        }

        /// <summary>
        /// Get total count of cells in the specified range without initalizing them all
        /// (which might cause serious performance issues on column-wide calculations).
        /// </summary>
        /// <param name="rangeExpression">Expression referring to the cell range.</param>
        /// <returns>Total number of cells in the range.</returns>
        internal static long GetTotalCellsCount(XObjectExpression rangeExpression)
        {
            var range = ((rangeExpression)?.Value as CellRangeReference)?.Range;
            if (range == null)
                return 0;
            return (long)(range.LastColumn().ColumnNumber() - range.FirstColumn().ColumnNumber() + 1) *
                   (long)(range.LastRow().RowNumber() - range.FirstRow().RowNumber() + 1);
        }
    }
}
