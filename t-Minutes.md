Development
- Introduce the dataset by describing it in terms
of its attributes (for the purpose of
anonymizing it and excluding the confidential
feedback)
- Prepare EDA to check the distribution of the
datasets in different languages, also datasets
used for department
- Present the Pipeline
- Discuss the result of the training and testing
the model by showing the performance
evaluation (accuracy, precision, f1-score, and
recall)

    ### Dataset Processing and Evaluation Metrics

    The predictive system was trained on a dataset comprising approximately 5,000 records of student feedback. The data exhibited natural class imbalances across both the broad feedback categories and the specific departmental targets. To ensure model stability during training, a preprocessing threshold was applied to the department targets, removing any department with fewer than 15 historical entries. Furthermore, algorithmic class weighting was applied to both phases to mitigate the remaining imbalance. The result of the training and testing of the models is discussed by showing the performance evaluation across four standard classification metrics: accuracy, precision, recall, and F1-score. The evaluation was split into two stages—Category Prediction and Department Prediction—reflecting the sequential nature of the proposed system architecture.

    ### Category Prediction Performance

    Table 1 illustrates the performance evaluation of various models in classifying the general category of the feedback (e.g., inquiry, complaint). Overall, the models demonstrated high proficiency in this stage. The **Support Vector Machine (SVM) utilizing a Hashing vectorizer** achieved the highest overall testing accuracy at 85.17%, along with a matching recall of 85.17%, indicating a strong ability to correctly identify true positive instances across categories. Meanwhile, **Logistic Regression with TF-IDF** yielded the highest precision (86.35%) and F1-Score (85.32%), minimizing false positives and providing the best balance between precision and recall. The strong performance across both traditional ML and transformer models (such as DistilBERT at 84.78% accuracy) indicates that broad feedback categories possess distinct, easily separable lexical patterns that the models can confidently identify.

    ### Department Prediction Performance

    Table 2 details the performance of the subsequent, more granular task of routing the feedback to specific departments. The **Support Vector Machine (SVM) with TF-IDF vectorization** emerged as the optimal model across all evaluated metrics, achieving an accuracy of 72.13%, a precision of 71.57%, a recall of 72.13%, and an F1-score of 70.17%. Given that departmental routing requires distinguishing between highly specific and often overlapping domains, the overall drop in performance metrics compared to the category prediction stage is expected. However, an F1-score of over 70% confirms the model's reliability in balancing false positives and false negatives for automated routing.

    ### Impact of Feature Extraction and Traditional ML vs. Deep Learning

    Across the testing phases of both prediction tasks, traditional machine learning algorithms consistently outperformed complex deep learning architectures across all four evaluation metrics. For the department prediction task, TF-IDF feature extraction proved superior for SVM and Logistic Regression, indicating that weighing the relative importance of specific, less-frequent keywords is critical for correct departmental routing.

    Conversely, advanced deep learning models—namely PyTorch CNN, mBERT, and DistilBERT—performed poorly on the department prediction task, with accuracies, precisions, recalls, and F1-scores all falling below 50%. In natural language processing, transformer architectures and deep neural networks typically require massive amounts of data to adjust their millions of parameters without overfitting. Their suboptimal performance across every metric in this study highlights that a dataset of roughly 5,000 rows is insufficient to effectively train or fine-tune these heavy architectures from scratch, allowing traditional statistical models to generalize significantly better on the available data.


- Discuss how the model analyzes the Tagalog
and Bisaya feedback
- Properly document the model for the
prediction
- Discuss the comparison of testing the MBert
and SVC, then what model is considered and
discuss why such model is being considered.
- Discuss the comparison of testing the
SVC-TFIDF, discuss the pipeline of these model
- Discuss the Logistic regression and NB
functionality, provide also the pipeline
- Discuss how the model is being trained





Testing
● Include the following test
- test of handling multi-lingual - Bisaya, Tagalog or
mix feedback - show how it was classified. Use
different scenarios
- test of machine learning to classify inquiry, concern,
suggestion, complaint and commendation and its
connection to the test of automatic routing to
designated department - present in different
scenarios
- test of automatic capture of the action taken
-present the






OUTPUT / PROJECT
● Specific objective 1 and 2 are not properly
implemented
● Set a threshold to reroute the feedback to
certain department or the inquiry, concern,
suggestion and commendation are directly
rerouted; and
● Human intervention will focus on on complaint
● Produce report for misclassified feedback
● Objective number 3 is not achieved, present in a
report form


● Must present how the model determines the multi-lingual feedback
For simple models such as: LogisticRegression, SVM, and ComplementNB with simple vectorizers with parameters: analyzer='char' and ngram_range=(3, 5).

This is the perfect way to anchor your methodology section. To cleanly and academically satisfy that specific requirement ("Must present how the model determines the multi-lingual feedback"), you need to explicitly state that **your models do not translate the text.** Instead of translating Cebuano or Tagalog into English, your models rely entirely on **mathematical pattern recognition** dictated by the vectorizer or tokenizer.

Because you used three different architectures, you have three distinct methods to present. Here is a structured breakdown you can adapt directly into your paper to fulfill that requirement.

---

    ## 1. Traditional ML (Logistic Regression, SVM, Naive Bayes)

    **The Method:** Character N-Grams (Sub-word Shapes)

    For your Scikit-Learn pipelines, you used `CountVectorizer(analyzer='char', ngram_range=(3, 5))`. This is your strongest defense for how the model handles highly unstructured, mixed languages like Taglish.

    * **How it handles multi-lingual text:** It completely bypasses the need for an English, Tagalog, or Cebuano dictionary by ignoring the concept of "words." It slices the text into overlapping chunks of 3 to 5 letters.
    * **The Paper Explanation:** Explain that when a user submits a Taglish sentence like *"Nag crash ang system"* (The system crashed), the vectorizer does not get confused by the mix of languages. It simply extracts character chunks like `nag`, `cra`, `rash`, and ` ang`. If the model historically sees `cra` and `rash` heavily associated with the "IT Support" class, it correctly classifies the ticket based purely on the spelling geometry, regardless of the surrounding Tagalog syntax.

    ---

    ## 2. PyTorch CNN

    **The Method:** Custom Dataset-Specific Vocabulary

    For your custom Deep Learning model, you used Regex whole-word tokenization (`\b\w+\b`) paired with an initialized `nn.Embedding` layer.

    * **How it handles multi-lingual text:** It builds a completely custom, localized dictionary based strictly on frequency.
    * **The Paper Explanation:** State that the PyTorch model handles Cebuano, Tagalog, and English by treating them all as one unified vocabulary. If the dataset contains 5,400 rows, the model tallies the top 20,000 most frequently used words. Therefore, English words ("password"), Tagalog words ("sira"), and Cebuano words ("guba") all securely earn a spot in the model's vocabulary. During training, the embedding layer mathematically groups these localized words together in vector space when they appear alongside similar target classes.

    ---

    ## 3. Hugging Face Transformers (DistilBERT/mBERT)

    **The Method:** Pre-Trained Cross-Lingual Embeddings & WordPiece Tokenization

    For your Transformer models, you used a pre-trained `distilbert-base-multilingual-cased` tokenizer and architecture.

    * **How it handles multi-lingual text:** It relies on a pre-existing, global understanding of 104 languages and breaks unknown words into smaller semantic roots.
    * **The Paper Explanation:** Note that DistilBERT inherently understands multi-lingual feedback because its foundational weights were trained on Wikipedia articles across over a hundred languages. When faced with a complex Tagalog or Cebuano affix, its **WordPiece tokenizer** breaks the word down into recognizable sub-words (roots and prefixes). It then plots these sub-words in a shared cross-lingual vector space, allowing it to natively understand that a local phrase and an English phrase share the same underlying intent without requiring explicit translation.

---
